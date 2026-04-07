<?php

namespace App\Http\Controllers;

use App\Models\LedgerEntry;
use App\Models\PersonalAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LedgerReportController extends Controller
{
    public function fundsOut(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'account_id' => 'required|exists:personal_accounts,id',
            'category' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $account = PersonalAccount::findOrFail($request->account_id);

        if ($account->balance < $request->amount) {
            return response()->json([
                'message' => 'Insufficient balance in selected account'
            ], 422);
        }

        // Determine credit account: GENERAL EXPENSES or null if you just log category
        $creditAccount = PersonalAccount::where('name', 'GENERAL EXPENSES')->first();

        DB::beginTransaction();

        try {
            // Debit: money leaving
            LedgerEntry::create([
                'debit_account_id' => $account->id,
                'credit_account_id' => $creditAccount?->id, // null if no account
                'type' => 'expense',
                'category' => $request->category,
                'amount' => $request->amount,
                'description' => $request->description,
                'created_by' => auth()->id(),
                'entry_date' => now()
            ]);

            // Update account balance
            $account->balance -= $request->amount;
            $account->save();

            DB::commit();

            return response()->json([
                'message' => 'Funds out recorded successfully',
                'amount' => $request->amount,
                'account' => $account->name,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to record funds out',
                'error' => $e->getMessage()
            ], 500);
        }
    }   

    public function adjust(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:personal_accounts,id',
            'difference' => 'required|numeric|not_in:0',
        ]);

        DB::transaction(function () use ($request) {

            $difference = (float) $request->difference;

            $account = PersonalAccount::findOrFail($request->account_id);

            // 🔑 Equity / Balance Adjustment account
            $equityAccount = PersonalAccount::where('name', 'Balance Adjustment')
                ->orWhere('account_type', 'equity')
                ->first();

            if (!$equityAccount) {
                abort(500, 'Balance Adjustment account not found.');
            }

            // Determine sides
            $debitAccount  = $difference > 0 ? $account : $equityAccount;
            $creditAccount = $difference > 0 ? $equityAccount : $account;

            $amount = abs($difference);

            // 1️⃣ Create ledger entry
            LedgerEntry::create([
                'entry_date' => now(),
                'debit_account_id' => $debitAccount->id,
                'credit_account_id' => $creditAccount->id,
                'amount' => $amount,
                'entry_type' => 'adjustment',
                'description' => $request->reason ?: 'Balance adjustment',
                'created_by' => auth()->id(),
            ]);

            // 2️⃣ Update balances
            $debitAccount->applyAdjustment($amount, 'debit');
            $creditAccount->applyAdjustment($amount, 'credit');
        });

        return response()->json(['message' => 'Adjustment posted and balances updated']);
    } 
}
