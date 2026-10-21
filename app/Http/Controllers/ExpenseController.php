<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\PersonalAccount;
use App\Models\ServiceProvider;
use App\Models\ProviderService;
use App\Services\AuditLogger;
use App\Services\LedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    /**
     * Display expenses and supporting data.
     */
    public function index()
    {
        $expenses = Expense::with(
            'serviceProvider',
            'providerService',
            'invoice'
        )->get();

        $accounts = PersonalAccount::get();
        $serviceProviders = ServiceProvider::get();
        $providerServices = ProviderService::get();

        $invoices = Invoice::with('customer')
            ->where('status', 'pending')
            ->get();

        return response()->json([
            'expenses' => $expenses,
            'serviceProviders' => $serviceProviders,
            'providerServices' => $providerServices,
            'invoices' => $invoices,
            'accounts' => $accounts,
        ]);
    }

    /**
     * Create an expense and process the associated financial entries.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:expense,provider_service,inventory,other',
            'invoice_id' => 'nullable|exists:invoices,id',
            'service_provider_id' => 'nullable|exists:service_providers,id',
            'provider_service_id' => 'nullable|exists:provider_services,id',
            'account_id' => 'required|exists:personal_accounts,id',
            'payment_method' => 'nullable|string|max:50',
            'amount' => 'nullable|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
            'expense_date' => 'nullable|date',
        ]);

        $result = DB::transaction(function () use ($validated) {
            $amount = $validated['amount'] ?? null;
            $invoiceId = $validated['invoice_id'] ?? null;

            // Lock the account while processing this transaction.
            $account = PersonalAccount::lockForUpdate()
                ->findOrFail($validated['account_id']);

            /*
             * Provider-service logic:
             * create the invoice/item if needed, without deducting
             * the account balance, matching the existing behavior.
             */
            if ($validated['type'] === 'provider_service') {
                if (
                    empty($validated['service_provider_id']) ||
                    empty($validated['provider_service_id'])
                ) {
                    throw new \Exception(
                        'Service provider and service are required'
                    );
                }

                $providerService = ProviderService::findOrFail(
                    $validated['provider_service_id']
                );

                if (!$amount) {
                    $amount = $providerService->price;
                }

                if (!$invoiceId) {
                    $provider = ServiceProvider::findOrFail(
                        $validated['service_provider_id']
                    );

                    $invoice = Invoice::create([
                        'invoice_type' => 'expense',
                        'vendor_name' => $provider->name,
                        'invoice_number' => 'INV-EXP-' . time(),
                        'invoice_date' => now(),
                        'due_date' => now()->addDays(7),
                        'total_amount' => $amount,
                        'status' => 'pending',
                    ]);

                    $invoiceId = $invoice->id;
                }

                InvoiceItem::create([
                    'invoice_id' => $invoiceId,
                    'item_type' => 'provider_service',
                    'provider_service_id' => $providerService->id,
                    'provider_service_name' => $providerService->name,
                    'expense_name' => null,
                    'quantity' => 1,
                    'unit_price' => $amount,
                    'line_total' => $amount,
                ]);
            }

            $expense = Expense::create([
                'type' => $validated['type'],
                'invoice_id' => $invoiceId,
                'service_provider_id' => $validated['service_provider_id'] ?? null,
                'provider_service_id' => $validated['provider_service_id'] ?? null,
                'account_id' => $account->id,
                'payment_method' => $validated['payment_method'] ?? null,
                'amount' => $amount,
                'description' => $validated['description'] ?? null,
                'expense_date' => $validated['expense_date'] ?? now(),
            ]);

            /*
             * Deduct account balance and create a ledger entry
             * for all types except provider_service.
             */
            if ($validated['type'] !== 'provider_service') {
                if ($account->balance < $amount) {
                    throw new \Exception('Insufficient account balance');
                }

                $account->balance -= $amount;
                $account->save();

                $expenseAccount = PersonalAccount::where(
                    'name',
                    'GENERAL EXPENSES'
                )->first();

                if (!$expenseAccount) {
                    throw new \Exception(
                        'GENERAL EXPENSES account is not configured'
                    );
                }

                LedgerService::recordExpense(
                    $expenseAccount,
                    $account,
                    $amount,
                    $validated['description'] ?? 'Expense #' . $expense->id
                );
            }

            return [
                'expense' => $expense,
                'invoice_id' => $invoiceId,
                'amount' => $amount,
                'account_id' => $account->id,
                'type' => $validated['type'],
            ];
        });

        // Record the audit event only after the transaction commits.
        $expense = $result['expense'];

        app(AuditLogger::class)->record(
            'expense.created',
            "Expense created (ID: {$expense->id})",
            $expense,
            [
                'expense_id' => $expense->id,
                'type' => $result['type'],
                'amount' => $result['amount'],
                'account_id' => $result['account_id'],
                'invoice_id' => $result['invoice_id'],
                'service_provider_id' => $expense->service_provider_id,
                'provider_service_id' => $expense->provider_service_id,
                'ledger_entry_expected' => $result['type'] !== 'provider_service',
            ],
            $request,
            auth('api')->id()
        );

        return response()->json([
            'message' => 'Expense recorded successfully',
            'expense' => $expense,
            'invoice_id' => $result['invoice_id'],
        ], 201);
    }

    /**
     * Display a specific expense.
     */
    public function show(string $id)
    {
        $expense = Expense::with([
            'serviceProvider',
            'providerService',
            'invoice',
        ])->findOrFail($id);

        return response()->json([
            'expense' => $expense,
        ]);
    }

    /**
     * Update an expense.
     */
    public function update(Request $request, string $id)
    {
        $expense = Expense::findOrFail($id);

        $request->validate([
            'invoice_id' => 'sometimes|nullable|exists:invoices,id',
            'service_provider_id' => 'required|exists:service_providers,id',
            'provider_service_id' => 'required|exists:provider_services,id',
            'amount' => 'required|numeric|min:1',
            'expense_date' => 'sometimes|nullable|date',
            'description' => 'nullable|string|max:20',
        ]);

        $fields = [
            'invoice_id',
            'service_provider_id',
            'provider_service_id',
            'amount',
            'expense_date',
            'description',
        ];

        $before = $expense->only($fields);

        $expense->update([
            'invoice_id' => $request->input('invoice_id', $expense->invoice_id),
            'service_provider_id' => $request->service_provider_id,
            'provider_service_id' => $request->provider_service_id,
            'amount' => $request->amount,
            'expense_date' => $request->input('expense_date', $expense->expense_date),
            'description' => $request->description,
        ]);

        $expense->refresh();

        $changes = [];

        foreach ($fields as $field) {
            $oldValue = $before[$field] ?? null;
            $newValue = $expense->getAttribute($field);

            if ($oldValue != $newValue) {
                $changes[$field] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        if (!empty($changes)) {
            app(AuditLogger::class)->record(
                'expense.updated',
                "Expense updated (ID: {$expense->id})",
                $expense,
                [
                    'expense_id' => $expense->id,
                    'changes' => $changes,
                ],
                $request,
                auth('api')->id()
            );
        }

        return response()->json([
            'message' => 'Expense updated successfully',
            'expense' => $expense,
        ]);
    }

    /**
     * Delete an expense.
     */
    public function destroy(string $id)
    {
        $expense = Expense::find($id);

        if (!$expense) {
            return response()->json([
                'message' => 'Expense not found',
            ], 404);
        }

        $expenseId = $expense->id;
        $expenseType = $expense->type;
        $amount = $expense->amount;
        $accountId = $expense->account_id;

        DB::transaction(function () use ($expense) {
            $expense->delete();
        });

        app(AuditLogger::class)->record(
            'expense.deleted',
            "Expense deleted (ID: {$expenseId})",
            $expense,
            [
                'expense_id' => $expenseId,
                'type' => $expenseType,
                'amount' => $amount,
                'account_id' => $accountId,
            ],
            request(),
            auth('api')->id()
        );

        return response()->json([
            'message' => 'Deleted',
        ]);
    }
}