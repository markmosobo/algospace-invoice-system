<?php

namespace App\Services;

use App\Models\LedgerEntry;
use App\Models\PersonalAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LedgerService
{
    /* =========================
       CASH / BANK SETTLEMENT
    ========================= */
    public static function settlePayment(
        PersonalAccount $paymentAccount,
        float $amount
    ) {
        $paymentAccount->refresh();

        if ($paymentAccount->balance < $amount) {
            throw new \Exception('Insufficient balance for payment');
        }

        $paymentAccount->balance -= $amount;
        $paymentAccount->save();
    }
        
    // =========================
    // Sales
    // =========================
    public static function recordSale($paymentAccount, $revenueAccount, $amount, $description)
    {
        return LedgerEntry::create([
            'entry_date' => now(),
            'debit_account_id' => $paymentAccount->id,
            'credit_account_id' => $revenueAccount->id,
            'amount' => $amount,
            'entry_type' => 'sale',
            'description' => $description,
            'created_by' => Auth::id(),
        ]);
    }

    // =========================
    // Expenses
    // =========================
    public static function recordExpense($expenseAccount, $paymentAccount, $amount, $description)
    {
        return LedgerEntry::create([
            'entry_date' => now(),
            'debit_account_id' => $expenseAccount->id,
            'credit_account_id' => $paymentAccount->id,
            'amount' => $amount,
            'entry_type' => 'expense',
            'description' => $description,
            'created_by' => Auth::id(),
        ]);
    }

    // =========================
    // Owner Draws
    // =========================
    public static function recordOwnerDraw(
        PersonalAccount $paymentAccount,
        float $amount,
        string $description
    ) {
        $ownerDrawAccount = PersonalAccount::where('name', 'OWNER DRAW')
            ->firstOrFail();

        return LedgerEntry::create([
            'entry_date' => now(),
            'debit_account_id'  => $ownerDrawAccount->id,
            'credit_account_id' => $paymentAccount->id,
            'amount' => $amount,
            'entry_type' => 'owner_draw',
            'description' => $description,
            'created_by' => auth()->id(),
        ]);
    }

    // =========================
    // Loans
    // =========================

    public static function giveLoan($paymentAccount, $amount, $description)
    {
        $loanReceivable = PersonalAccount::where('name', 'LOAN RECEIVABLE')->first();
        return LedgerEntry::create([
            'entry_date' => now(),
            'debit_account_id' => $loanReceivable->id,
            'credit_account_id' => $paymentAccount->id,
            'amount' => $amount,
            'entry_type' => 'loan',
            'description' => $description,
            'created_by' => Auth::id(),
        ]);
    }

    // Loan In
    public static function recordLoan(PersonalAccount $account, float $amount, string $description)
    {
        $loanReceivable = PersonalAccount::where('name', 'LOAN RECEIVABLE')->firstOrFail();

        return LedgerEntry::create([
            'entry_date' => now(),
            'debit_account_id' => $account->id,
            'credit_account_id' => $loanReceivable->id,
            'amount' => $amount,
            'entry_type' => 'loan_in',
            'description' => $description,
            'created_by' => Auth::id(),
        ]);
    }

    // Loan Out (repayment)
    public static function recordLoanRepayment(PersonalAccount $account, float $amount, string $description)
    {
        $loanPayable = PersonalAccount::where('name', 'LOAN PAYABLE')->firstOrFail();

        return LedgerEntry::create([
            'entry_date' => now(),
            'debit_account_id' => $loanPayable->id,
            'credit_account_id' => $account->id,
            'amount' => $amount,
            'entry_type' => 'loan_out',
            'description' => $description,
            'created_by' => Auth::id(),
        ]);
    }
    
    public static function recordTithe(
        PersonalAccount $paymentAccount,
        $from = null,
        $to = null,
        $percentage = 0.1
    ) {
        $titheAmount = LedgerReportService::getTitheAmount($from, $to, $percentage);

        if ($titheAmount <= 0) {
            return null;
        }

        $titheAccount = PersonalAccount::where('name', 'TITHE ACCOUNT')->firstOrFail();

        DB::transaction(function () use (
            $paymentAccount,
            $titheAccount,
            $titheAmount,
            $from,
            $to
        ) {

            // ✅ 1. Deduct cash/mpesa/bank
            self::settlePayment($paymentAccount, $titheAmount);

            // ✅ 2. Record ledger (truth)
            self::recordExpense(
                $titheAccount,
                $paymentAccount,
                $titheAmount,
                'Tithe payment from ' .
                ($from ?? 'start') .
                ' to ' .
                ($to ?? now()->toDateString())
            );
        });
    }

        /* =========================
       FIRST FRUITS
    ========================= */
public static function recordFirstFruits(
    PersonalAccount $paymentAccount,
    float $amount,               // <-- use the amount passed
    string $description = 'First Fruits'
) {
    if ($amount <= 0) return null;

    $firstFruitsAccount = PersonalAccount::where('name', 'FIRST FRUITS ACCOUNT')->firstOrFail();

    DB::transaction(function () use ($paymentAccount, $firstFruitsAccount, $amount, $description) {
        // Deduct from payment account
        self::settlePayment($paymentAccount, $amount);

        // Record ledger entry
        self::recordExpense(
            $firstFruitsAccount,
            $paymentAccount,
            $amount,
            $description,
            'first_fruits'
        );
    });
}


    /* =========================
       CAPITAL INJECTION
    ========================= */
    public static function recordCapitalInjection(PersonalAccount $account, float $amount, string $description = 'Capital injection')
    {
        $capitalAccount = PersonalAccount::where('name', 'CAPITAL ACCOUNT')->firstOrFail();

        DB::transaction(function () use ($account, $capitalAccount, $amount, $description) {

            LedgerEntry::create([
                'entry_date' => now(),
                'debit_account_id' => $account->id,
                'credit_account_id' => $capitalAccount->id,
                'amount' => $amount,
                'entry_type' => 'capital_injection',
                'description' => $description,
                'created_by' => Auth::id(),
            ]);
        });
    }

    /* =========================
       INTER-ACCOUNT TRANSFER
    ========================= */
    public static function transferFunds(PersonalAccount $from, PersonalAccount $to, float $amount, string $description = 'Inter-account transfer')
    {
        if ($from->id === $to->id) {
            throw new \Exception('Cannot transfer to the same account');
        }

        DB::transaction(function () use ($from, $to, $amount, $description) {
            self::settlePayment($from, $amount); // only deduct from 'from' account
            $to->increment('balance', $amount);  // only add to 'to' account

            LedgerEntry::create([
                'entry_date' => now(),
                'debit_account_id' => $to->id,
                'credit_account_id' => $from->id,
                'amount' => $amount,
                'entry_type' => 'transfer',
                'description' => $description,
                'created_by' => Auth::id(),
            ]);
        });
    }

    public static function recordOwnerRedeposit(
        PersonalAccount $account,
        float $amount,
        string $note = 'Owner funds redeposited'
    ) {
        $equityAccount = PersonalAccount::where('name', 'OWNER EQUITY')->firstOrFail();

        DB::transaction(function () use ($account, $equityAccount, $amount, $note) {

            // ✅ Increase the asset (savings / bank / cash)
            $account->increment('balance', $amount);

            // ✅ Record ledger entry
            LedgerEntry::create([
                'entry_date'        => now(),
                'debit_account_id'  => $account->id,        // Asset ↑
                'credit_account_id' => $equityAccount->id,  // Equity ↑
                'amount'            => $amount,
                'entry_type'        => 'owner_redeposit',
                'description'       => $note,
                'created_by'        => auth()->id(),
            ]);
        });
    }



}
