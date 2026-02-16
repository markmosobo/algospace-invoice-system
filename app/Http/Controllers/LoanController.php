<?php

namespace App\Http\Controllers;

use App\Models\PersonalAccount;
use App\Services\LedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    public function loanIn(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:personal_accounts,id',
            'amount' => 'required|numeric|min:1',
        ]);

        DB::transaction(function () use ($request) {
            $account = PersonalAccount::lockForUpdate()->findOrFail($request->account_id);
            $account->increment('balance', $request->amount);

            // Record the loan in ledger
            LedgerService::recordLoan(
                $account,
                $request->amount,
                'Loan received'
            );
        });

        return response()->json(['message' => 'Loan recorded']);
    }

    public function repay(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:personal_accounts,id',
            'amount' => 'required|numeric|min:1',
        ]);

        DB::transaction(function () use ($request) {
            $account = PersonalAccount::lockForUpdate()->findOrFail($request->account_id);

            if ($account->balance < $request->amount) {
                throw new \Exception('Insufficient funds.');
            }

            $account->decrement('balance', $request->amount);

            LedgerService::recordLoanRepayment(
                $account,
                $request->amount,
                'Loan repayment'
            );
        });

        return response()->json(['message' => 'Loan repaid']);
    }
   
}
