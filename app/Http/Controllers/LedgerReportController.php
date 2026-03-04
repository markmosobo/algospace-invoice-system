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
}
