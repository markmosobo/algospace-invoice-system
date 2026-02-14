<?php

namespace App\Services;

use App\Models\LedgerEntry;
use App\Models\PersonalAccount;

class LedgerReportService
{
    /**
     * Get Profit & Loss report
     *
     * @param string|null $from  YYYY-MM-DD
     * @param string|null $to    YYYY-MM-DD
     * @return array
     */
    public static function getProfitLoss($from = null, $to = null)
    {
        $query = LedgerEntry::query();

        if ($from) $query->where('entry_date', '>=', $from);
        if ($to)   $query->where('entry_date', '<=', $to);

        // Revenue: all ledger entries crediting SALES REVENUE accounts
        $revenue = $query->whereHas('creditAccount', function ($q) {
            $q->where('name', 'SALES REVENUE');
        })->sum('amount');

        // Expenses: all ledger entries debiting expense accounts
        $expenses = $query->whereHas('debitAccount', function ($q) {
            $q->where('category', 'expense');
        })->sum('amount');

        // Owner draws
        $ownerDraws = $query->where('entry_type', 'owner_draw')->sum('amount');

        // Net Profit = Revenue - Expenses - Owner Draws
        $profit = $revenue - $expenses - $ownerDraws;

        // Tithe (10% of profit before owner draws)
        $tithe = self::calculateTithe($revenue, $expenses);
        $accounts = PersonalAccount::where('category', 'shop_working_capital')
                ->whereIn('account_type', ['cash', 'mpesa', 'bank'])
                ->where('balance', '>', 0)
                ->get(['id','name','balance','account_type','category']);

        return [
            'revenue' => $revenue,
            'expenses' => $expenses,
            'owner_draws' => $ownerDraws,
            'profit' => $profit,
            'tithe' => $tithe,
            'personalAccounts' => $accounts,
        ];
    }

    /**
     * Calculate tithe
     * Default: 10% of profit (revenue - expenses)
     * 
     * @param float $revenue
     * @param float $expenses
     * @param float $percentage
     * @return float
     */
    public static function calculateTithe($revenue, $expenses, $percentage = 0.1)
    {
        $profitBeforeDraws = $revenue - $expenses;
        if ($profitBeforeDraws <= 0) return 0;
        return round($profitBeforeDraws * $percentage, 2);
    }

    /**
     * Get total revenue
     */
    public static function getTotalRevenue($from = null, $to = null)
    {
        $query = LedgerEntry::query();
        if ($from) $query->where('entry_date', '>=', $from);
        if ($to)   $query->where('entry_date', '<=', $to);

        return $query->whereHas('creditAccount', function ($q) {
            $q->where('name', 'SALES REVENUE');
        })->sum('amount');
    }

    /**
     * Get total expenses
     */
    public static function getTotalExpenses($from = null, $to = null)
    {
        $query = LedgerEntry::query();
        if ($from) $query->where('entry_date', '>=', $from);
        if ($to)   $query->where('entry_date', '<=', $to);

        return $query->whereHas('debitAccount', function ($q) {
            $q->where('category', 'expense');
        })->sum('amount');
    }

    /**
     * Get owner draws
     */
    public static function getOwnerDraws($from = null, $to = null)
    {
        $query = LedgerEntry::query();
        if ($from) $query->where('entry_date', '>=', $from);
        if ($to)   $query->where('entry_date', '<=', $to);

        return $query->where('entry_type', 'owner_draw')->sum('amount');
    }

    public static function getTitheAmount($from = null, $to = null, $percentage = 0.1)
    {
        $report = self::getProfitLoss($from, $to);
        return $report['tithe'];
    }
    
}
