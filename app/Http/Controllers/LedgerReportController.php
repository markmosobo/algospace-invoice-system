<?php

namespace App\Http\Controllers;

use App\Models\LedgerEntry;
use App\Models\PersonalAccount;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LedgerReportController extends Controller
{
    public function fundsOut(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:1',
            'account_id' => 'required|exists:personal_accounts,id',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
        ]);

        $result = DB::transaction(function () use ($data, $request) {
            // Lock the account to prevent concurrent overspending.
            $account = PersonalAccount::whereKey($data['account_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $amount = (float) $data['amount'];

            if ($account->balance < $amount) {
                return [
                    'insufficient_balance' => true,
                ];
            }

            $creditAccount = PersonalAccount::where(
                'name',
                'GENERAL EXPENSES'
            )->first();

            $entry = LedgerEntry::create([
                'debit_account_id' => $account->id,
                'credit_account_id' => $creditAccount?->id,
                'type' => 'expense',
                'category' => $data['category'],
                'amount' => $amount,
                'description' => $data['description'] ?? null,
                'created_by' => auth('api')->id(),
                'entry_date' => now(),
            ]);

            $account->balance -= $amount;
            $account->save();

            app(AuditLogger::class)->record(
                'ledger.funds_out_recorded',
                'Funds-out transaction recorded',
                $entry,
                [
                    'ledger_entry_id' => $entry->id,
                    'account_id' => $account->id,
                    'amount' => $amount,
                    'category' => $data['category'],
                    'credit_account_id' => $creditAccount?->id,
                ],
                $request,
                auth('api')->id()
            );

            return [
                'insufficient_balance' => false,
                'amount' => $amount,
                'account_name' => $account->name,
            ];
        });

        if ($result['insufficient_balance']) {
            return response()->json([
                'message' => 'Insufficient balance in selected account',
            ], 422);
        }

        return response()->json([
            'message' => 'Funds out recorded successfully',
            'amount' => $result['amount'],
            'account' => $result['account_name'],
        ]);
    }

    public function adjust(Request $request)
    {
        $data = $request->validate([
            'account_id' => 'required|exists:personal_accounts,id',
            'difference' => 'required|numeric|not_in:0',
            'reason' => 'nullable|string|max:1000',
        ]);

        $entry = DB::transaction(function () use ($data, $request) {
            $difference = (float) $data['difference'];

            $account = PersonalAccount::whereKey($data['account_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $equityAccount = PersonalAccount::where(
                'name',
                'Balance Adjustment'
            )
                ->orWhere('account_type', 'equity')
                ->first();

            if (!$equityAccount) {
                abort(500, 'Balance Adjustment account not found.');
            }

            $debitAccount = $difference > 0
                ? $account
                : $equityAccount;

            $creditAccount = $difference > 0
                ? $equityAccount
                : $account;

            $amount = abs($difference);

            $entry = LedgerEntry::create([
                'entry_date' => now(),
                'debit_account_id' => $debitAccount->id,
                'credit_account_id' => $creditAccount->id,
                'amount' => $amount,
                'entry_type' => 'adjustment',
                'description' => $data['reason'] ?? 'Balance adjustment',
                'created_by' => auth('api')->id(),
            ]);

            $debitAccount->applyAdjustment($amount, 'debit');
            $creditAccount->applyAdjustment($amount, 'credit');

            app(AuditLogger::class)->record(
                'ledger.balance_adjusted',
                'Account balance adjustment recorded',
                $entry,
                [
                    'ledger_entry_id' => $entry->id,
                    'account_id' => $account->id,
                    'difference' => $difference,
                    'amount' => $amount,
                    'debit_account_id' => $debitAccount->id,
                    'credit_account_id' => $creditAccount->id,
                ],
                $request,
                auth('api')->id()
            );

            return $entry;
        });

        return response()->json([
            'message' => 'Adjustment posted and balances updated',
        ]);
    }
}