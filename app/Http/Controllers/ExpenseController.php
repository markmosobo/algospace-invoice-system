<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\PersonalAccount;
use App\Models\ServiceProvider;
use App\Models\ProviderService;
use App\Models\SystemLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\LedgerService;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::with('serviceProvider', 'providerService', 'invoice')->get();
        $accounts = PersonalAccount::get();
        $serviceProviders = ServiceProvider::get();
        $providerServices = ProviderService::get();
        $invoices =Invoice::with('customer')->where('status', 'pending')->get();

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' retrieved expenses'
        ]);

        // Return as JSON
        return response()->json([
            'expenses' => $expenses,
            'serviceProviders' => $serviceProviders,
            'providerServices' => $providerServices,
            'invoices' => $invoices,
            'accounts' => $accounts
        ]);
    } 
    

public function store(Request $request)
{
    $request->validate([
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

    DB::transaction(function () use ($request, &$expense, &$invoiceId) {

        $amount    = $request->amount;
        $invoiceId = $request->invoice_id;

        /* ===============================
           LOCK ACCOUNT
        =============================== */
        $account = PersonalAccount::lockForUpdate()
            ->findOrFail($request->account_id);

        /* ===============================
           PROVIDER SERVICE LOGIC (NO DEDUCTION)
        =============================== */
        if ($request->type === 'provider_service') {

            if (!$request->service_provider_id || !$request->provider_service_id) {
                throw new \Exception('Service provider and service are required');
            }

            $providerService = ProviderService::findOrFail(
                $request->provider_service_id
            );

            // If amount not provided, use service price
            if (!$amount) {
                $amount = $providerService->price;
            }

            // Create invoice if missing
            if (!$invoiceId) {
                $provider = ServiceProvider::findOrFail(
                    $request->service_provider_id
                );

                $invoice = Invoice::create([
                    'invoice_type'   => 'expense',
                    'vendor_name'    => $provider->name,
                    'invoice_number' => 'INV-EXP-' . time(),
                    'invoice_date'   => now(),
                    'due_date'       => now()->addDays(7),
                    'total_amount'   => $amount,
                    'status'         => 'pending',
                ]);

                $invoiceId = $invoice->id;
            }

            // Create invoice item
            InvoiceItem::create([
                'invoice_id' => $invoiceId,
                'item_type'  => 'provider_service',
                'provider_service_id'   => $providerService->id,
                'provider_service_name' => $providerService->name,
                'expense_name' => null,
                'quantity'     => 1,
                'unit_price'   => $amount,
                'line_total'   => $amount,
            ]);
        }

        /* ===============================
           CREATE EXPENSE (ALL TYPES)
        =============================== */
        $expense = Expense::create([
            'type' => $request->type,
            'invoice_id' => $invoiceId,
            'service_provider_id' => $request->service_provider_id,
            'provider_service_id' => $request->provider_service_id,
            'account_id' => $account->id,
            'payment_method' => $request->payment_method,
            'amount' => $amount,
            'description' => $request->description,
            'expense_date' => $request->expense_date ?? now(),
        ]);

        /* ===============================
           DEDUCT BALANCE (NOT provider_service)
        =============================== */
        if ($request->type !== 'provider_service') {

            if ($account->balance < $amount) {
                throw new \Exception('Insufficient account balance');
            }

            // Deduct cash/bank/mpesa
            $account->balance -= $amount;
            $account->save();

            // ===============================
            // LEDGER ENTRY (EXPENSE)
            // ===============================
            $expenseAccount = PersonalAccount::where('name', 'GENERAL EXPENSES')->first();

            if (!$expenseAccount) {
                throw new \Exception('GENERAL EXPENSES account is not configured');
            }

            LedgerService::recordExpense(
                $expenseAccount,   // debit
                $account,          // credit (cash/mpesa/bank)
                $amount,
                $request->description ?? 'Expense #' . $expense->id
            );
        }


        /* ===============================
           SYSTEM LOG
        =============================== */
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' =>
                auth('api')->user()->name .
                ' created expense #' . $expense->id
        ]);
    });

    return response()->json([
        'message' => 'Expense recorded successfully',
        'expense' => $expense,
        'invoice_id' => $invoiceId
    ], 201);
}

    
    public function show(string $id)
    {
        $expense = Expense::with(['serviceProvider', 'providerService', 'invoice'])
                        ->findOrFail($id);

        return response()->json([
            'expense' => $expense
        ]);
    }
  
    
    public function update(Request $request, string $id)
    {
        $expense = Expense::findOrFail($id);

        $request->validate([
            'invoice_id'   => 'sometimes|exists:invoices,id',
            'service_provider_id'   => 'required|exists:service_providers,id',
            'provider_service_id'   => 'required|exists:provider_services,id',
            'amount'       => 'required|numeric|min:1',
            // 'expense_date' => 'required|date',
            'description' => 'nullable|string|max:20',
        ]);

        $expense->update([
            'invoice_id'   => $request->invoice_id,
            'service_provider_id'   => $request->service_provider_id,
            'provider_service_id'   => $request->provider_service_id,
            'amount'       => $request->amount,
            'expense_date' => $request->expense_date,
            'description'      => $request->description ?? null,
        ]);

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' updated expense #'.$expense->id
        ]);         

        return response()->json([
            'message' => 'Expense updated successfully',
            'expense' => $expense
        ]);
    }
    
    public function destroy(string $id)
    {
        Expense::destroy($id);

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' deleted expense #'.$id
        ]); 

        return response()->json(['message' => 'Deleted']);
    }    
}
