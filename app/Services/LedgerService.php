<?php

namespace App\Services;

use App\Models\LedgerEntry;
use App\Models\PersonalAccount;
use Illuminate\Support\Facades\Auth;

class LedgerService
{
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
    public static function recordOwnerDraw($paymentAccount, $amount, $description)
    {
        $shopCapital = PersonalAccount::where('category','shop_working_capital')->first();
        return LedgerEntry::create([
            'entry_date' => now(),
            'debit_account_id' => $paymentAccount->id,
            'credit_account_id' => $shopCapital->id,
            'amount' => $amount,
            'entry_type' => 'owner_draw',
            'description' => $description,
            'created_by' => Auth::id(),
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
    
    public static function recordTithe(PersonalAccount $paymentAccount, $from = null, $to = null, $percentage = 0.1)
    {
        $titheAmount = \App\Services\LedgerReportService::getTitheAmount($from, $to, $percentage);

        if ($titheAmount <= 0) {
            return null; // nothing to record
        }

        // Get the tithe ledger account
        $titheAccount = PersonalAccount::where('name', 'TITHE ACCOUNT')->first();
        if (!$titheAccount) {
            throw new \Exception('Tithe account not found. Please create it first.');
        }

        // Make ledger entry
        return self::recordExpense(
            $titheAccount,       // debit tithe account
            $paymentAccount,     // credit cash/bank/mpesa
            $titheAmount,
            'Tithe payment from ' . ($from ?? 'start') . ' to ' . ($to ?? now()->toDateString())
        );
    }
}
