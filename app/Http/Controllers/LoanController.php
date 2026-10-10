<?php

namespace App\Http\Controllers;

use App\Models\PersonalAccount;
use App\Services\AuditLogger;
use App\Services\LedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LoanController extends Controller
{
    public function loanIn(Request $request)
    {
        $data = $request->validate([
            'account_id' => 'required|exists:personal_accounts,id',
            'amount' => 'required|numeric|min:1',
        ]);

        DB::transaction(function () use ($data, $request) {
            $account = PersonalAccount::lockForUpdate()
                ->findOrFail($data['account_id']);

            $amount = (float) $data['amount'];

            $account->increment('balance', $amount);

            $entry = LedgerService::recordLoan(
                $account,
                $amount,
                'Loan received'
            );

            app(AuditLogger::class)->record(
                'loan.received',
                'Loan received and recorded',
                $entry instanceof \Illuminate\Database\Eloquent\Model
                    ? $entry
                    : null,
                [
                    'account_id' => $account->id,
                    'amount' => $amount,
                    'ledger_entry_id' => $entry?->id,
                ],
                $request,
                auth('api')->id()
            );
        });

        return response()->json([
            'message' => 'Loan recorded',
        ]);
    }

    public function repay(Request $request)
    {
        $data = $request->validate([
            'account_id' => 'required|exists:personal_accounts,id',
            'amount' => 'required|numeric|min:1',
        ]);

        DB::transaction(function () use ($data, $request) {
            $account = PersonalAccount::lockForUpdate()
                ->findOrFail($data['account_id']);

            $amount = (float) $data['amount'];

            if ($account->balance < $amount) {
                throw ValidationException::withMessages([
                    'amount' => ['Insufficient funds.'],
                ]);
            }

            $account->decrement('balance', $amount);

            $entry = LedgerService::recordLoanRepayment(
                $account,
                $amount,
                'Loan repayment'
            );

            app(AuditLogger::class)->record(
                'loan.repayment_recorded',
                'Loan repayment recorded',
                $entry instanceof \Illuminate\Database\Eloquent\Model
                    ? $entry
                    : null,
                [
                    'account_id' => $account->id,
                    'amount' => $amount,
                    'ledger_entry_id' => $entry?->id,
                ],
                $request,
                auth('api')->id()
            );
        });

        return response()->json([
            'message' => 'Loan repaid',
        ]);
    }
}