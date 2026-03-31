<?php

namespace App\Services\AI;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinancialSnapshotService
{
    public static function thisWeek(): array
    {
        $start = Carbon::now()->startOfWeek();
        $end   = Carbon::now()->endOfWeek();

        $payments = DB::table('payments')
            ->whereBetween('created_at', [$start, $end])
            ->sum('amount');

        $expenses = DB::table('expenses')
            ->whereBetween('created_at', [$start, $end])
            ->sum('amount');

        $farmExpenses = DB::table('farm_expenses')
            ->whereBetween('created_at', [$start, $end])
            ->sum('amount');

        $unpaidInvoices = DB::table('invoices')
            ->where('status', 'unpaid')
            ->count();

        return [
            'scope' => 'global',
            'period' => 'this_week',
            'income' => (float) $payments,
            'expenses' => (float) $expenses,
            'farm_expenses' => (float) $farmExpenses,
            'net' => $payments - ($expenses + $farmExpenses),
            'unpaid_invoices' => $unpaidInvoices,
        ];
    }

    public static function thisMonth(): array
    {
        $start = Carbon::now()->startOfMonth();
        $end   = Carbon::now()->endOfMonth();

        $payments = DB::table('payments')
            ->whereBetween('created_at', [$start, $end])
            ->sum('amount');

        $expenses = DB::table('expenses')
            ->whereBetween('created_at', [$start, $end])
            ->sum('amount');

        $farmExpenses = DB::table('farm_expenses')
            ->whereBetween('created_at', [$start, $end])
            ->sum('amount');

        return [
            'scope' => 'global',
            'period' => 'this_month',
            'income' => (float) $payments,
            'expenses' => (float) $expenses,
            'farm_expenses' => (float) $farmExpenses,
            'net' => $payments - ($expenses + $farmExpenses),
        ];
    }

    public static function last3Months(): array
    {
        $months = collect();

        for ($i = 2; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);

            $payments = DB::table('payments')
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->sum('amount');

            $expenses = DB::table('expenses')
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->sum('amount');

            $farmExpenses = DB::table('farm_expenses')
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->sum('amount');

            $months->push([
                'scope' => 'global',
                'month' => $month->format('F Y'),
                'income' => (float) $payments,
                'expenses' => (float) $expenses,
                'farm_expenses' => (float) $farmExpenses,
                'net' => $payments - ($expenses + $farmExpenses),
            ]);
        }

        return $months->toArray();
    }

    public static function invoiceHealth(): array
    {
        $totalInvoices = DB::table('invoices')->count();

        $paidInvoices = DB::table('invoices')
            ->where('status', 'paid')
            ->count();

        $overdueInvoices = DB::table('invoices')
            ->where('status', 'unpaid')
            ->whereDate('due_date', '<', now())
            ->count();

        return [
            'scope' => 'global',
            'total' => $totalInvoices,
            'paid' => $paidInvoices,
            'overdue' => $overdueInvoices,
            'collection_rate' => $totalInvoices > 0
                ? round(($paidInvoices / $totalInvoices) * 100, 1)
                : null,
        ];
    }

    public static function overview(): array
    {
        return [
            'week' => self::thisWeek(),
            'month' => self::thisMonth(),
            'invoice_health' => self::invoiceHealth(),
        ];
    }
}