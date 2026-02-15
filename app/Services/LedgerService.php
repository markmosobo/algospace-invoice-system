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

    public static function receiveLoan($paymentAccount, $amount, $description)
    {
        $loanPayable = PersonalAccount::where('name', 'LOAN PAYABLE')->first();
        return LedgerEntry::create([
            'entry_date' => now(),
            'debit_account_id' => $paymentAccount->id,
            'credit_account_id' => $loanPayable->id,
            'amount' => $amount,
            'entry_type' => 'loan',
            'description' => $description,
            'created_by' => Auth::id(),
        ]);
    }

    public static function repayLoan($fromAccount, $toLoanAccount, $amount, $description)
    {
        return LedgerEntry::create([
            'entry_date' => now(),
            'debit_account_id' => $toLoanAccount->id,
            'credit_account_id' => $fromAccount->id,
            'amount' => $amount,
            'entry_type' => 'loan_repayment',
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

}
