<?php

namespace App\Http\Controllers;

use App\Models\PersonalAccount;
use App\Models\PersonalCategory;
use App\Models\PersonalTransaction;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PersonalTransactionController extends Controller
{
    /**
     * Display transactions, accounts and categories.
     */
    public function index()
    {
        $accounts = PersonalAccount::all();
        $categories = PersonalCategory::all();

        $personalTransactions = PersonalTransaction::with(
            'account',
            'category'
        )->get();

        return response()->json([
            'personalTransactions' => $personalTransactions,
            'accounts' => $accounts,
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created transaction.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'account_id' => 'required|exists:personal_accounts,id',
            'category_id' => 'nullable|exists:personal_categories,id',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'transaction_date' => 'nullable|date',
        ]);

        $personalTransaction = DB::transaction(function () use (
            $data,
            $request
        ) {
            $account = PersonalAccount::whereKey($data['account_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $amount = (float) $data['amount'];

            $transaction = PersonalTransaction::create([
                'account_id' => $account->id,
                'category_id' => $data['category_id'] ?? null,
                'type' => $data['type'],
                'amount' => $amount,
                'payment_method' => $data['payment_method'] ?? null,
                'description' => $data['description'] ?? null,
                'transaction_date' => $data['transaction_date'] ?? now(),
            ]);

            $this->applyBalanceEffect($account, $data['type'], $amount);
            $account->save();

            app(AuditLogger::class)->record(
                'personal_transaction.created',
                'Personal transaction created',
                $transaction,
                [
                    'transaction_id' => $transaction->id,
                    'account_id' => $account->id,
                    'category_id' => $transaction->category_id,
                    'type' => $transaction->type,
                    'amount' => $amount,
                    'transaction_date' => $transaction->transaction_date,
                    'account_balance_after' => $account->balance,
                ],
                $request,
                auth('api')->id()
            );

            return $transaction;
        });

        return response()->json([
            'message' => 'Personal transaction created successfully',
            'transaction' => $personalTransaction,
        ], 201);
    }

    /**
     * Display the specified transaction.
     */
    public function show(string $id)
    {
        $transaction = PersonalTransaction::find($id);

        if (!$transaction) {
            return response()->json([
                'message' => 'Transaction not found',
            ], 404);
        }

        return response()->json($transaction);
    }

    /**
     * Update a transaction and reconcile account balances.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'account_id' => 'sometimes|required|exists:personal_accounts,id',
            'category_id' => 'sometimes|nullable|exists:personal_categories,id',
            'type' => 'sometimes|required|in:income,expense',
            'amount' => 'sometimes|required|numeric|min:0',
            'payment_method' => 'sometimes|nullable|string|max:50',
            'description' => 'sometimes|nullable|string',
            'transaction_date' => 'sometimes|nullable|date',
        ]);

        $transaction = DB::transaction(function () use (
            $data,
            $id,
            $request
        ) {
            $transaction = PersonalTransaction::whereKey($id)
                ->lockForUpdate()
                ->first();

            if (!$transaction) {
                return null;
            }

            $before = [
                'account_id' => $transaction->account_id,
                'category_id' => $transaction->category_id,
                'type' => $transaction->type,
                'amount' => (float) $transaction->amount,
                'transaction_date' => $transaction->transaction_date,
            ];

            $oldAccountId = (int) $transaction->account_id;
            $newAccountId = (int) ($data['account_id'] ?? $oldAccountId);

            // Lock account rows in a consistent order.
            $accountIds = array_values(array_unique([
                $oldAccountId,
                $newAccountId,
            ]));

            sort($accountIds);

            $accounts = PersonalAccount::whereIn('id', $accountIds)
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $oldAccount = $accounts->get($oldAccountId);
            $newAccount = $accounts->get($newAccountId);

            if (!$oldAccount || !$newAccount) {
                abort(404, 'Personal account not found');
            }

            // Reverse the original transaction's balance effect.
            $this->reverseBalanceEffect(
                $oldAccount,
                $transaction->type,
                (float) $transaction->amount
            );

            // Apply the updated transaction's balance effect.
            $newType = $data['type'] ?? $transaction->type;
            $newAmount = (float) ($data['amount'] ?? $transaction->amount);

            $this->applyBalanceEffect(
                $newAccount,
                $newType,
                $newAmount
            );

            $oldAccount->save();

            if ($newAccountId !== $oldAccountId) {
                $newAccount->save();
            }

            // Update only fields provided by the request.
            foreach ([
                'account_id',
                'category_id',
                'type',
                'amount',
                'payment_method',
                'description',
                'transaction_date',
            ] as $field) {
                if (array_key_exists($field, $data)) {
                    $transaction->{$field} = $data[$field];
                }
            }

            $transaction->save();

            app(AuditLogger::class)->record(
                'personal_transaction.updated',
                'Personal transaction updated',
                $transaction,
                [
                    'transaction_id' => $transaction->id,
                    'before' => $before,
                    'after' => [
                        'account_id' => $transaction->account_id,
                        'category_id' => $transaction->category_id,
                        'type' => $transaction->type,
                        'amount' => (float) $transaction->amount,
                        'transaction_date' => $transaction->transaction_date,
                    ],
                    'old_account_balance_after' => $oldAccount->balance,
                    'new_account_balance_after' => $newAccount->balance,
                ],
                $request,
                auth('api')->id()
            );

            return $transaction;
        });

        if (!$transaction) {
            return response()->json([
                'message' => 'Transaction not found',
            ], 404);
        }

        return response()->json([
            'message' => 'Transaction updated successfully',
            'transaction' => $transaction,
        ]);
    }

    /**
     * Delete a transaction and reverse its balance effect.
     */
    public function destroy(string $id)
    {
        $deleted = DB::transaction(function () use ($id) {
            $transaction = PersonalTransaction::whereKey($id)
                ->lockForUpdate()
                ->first();

            if (!$transaction) {
                return false;
            }

            $account = PersonalAccount::whereKey($transaction->account_id)
                ->lockForUpdate()
                ->firstOrFail();

            $amount = (float) $transaction->amount;

            $this->reverseBalanceEffect(
                $account,
                $transaction->type,
                $amount
            );

            $account->save();

            app(AuditLogger::class)->record(
                'personal_transaction.deleted',
                'Personal transaction deleted',
                $transaction,
                [
                    'transaction_id' => $transaction->id,
                    'account_id' => $transaction->account_id,
                    'category_id' => $transaction->category_id,
                    'type' => $transaction->type,
                    'amount' => $amount,
                    'transaction_date' => $transaction->transaction_date,
                    'account_balance_after' => $account->balance,
                ],
                request(),
                auth('api')->id()
            );

            $transaction->delete();

            return true;
        });

        if (!$deleted) {
            return response()->json([
                'message' => 'Transaction not found',
            ], 404);
        }

        return response()->json([
            'message' => 'Transaction deleted successfully',
        ]);
    }

    /**
     * Apply a transaction's effect to an account balance.
     */
    private function applyBalanceEffect(
        PersonalAccount $account,
        string $type,
        float $amount
    ): void {
        if ($type === 'income') {
            $account->balance += $amount;
        } else {
            $account->balance -= $amount;
        }
    }

    /**
     * Reverse a transaction's original effect on an account balance.
     */
    private function reverseBalanceEffect(
        PersonalAccount $account,
        string $type,
        float $amount
    ): void {
        if ($type === 'income') {
            $account->balance -= $amount;
        } else {
            $account->balance += $amount;
        }
    }
}