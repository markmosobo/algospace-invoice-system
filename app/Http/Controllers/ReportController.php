<?php

namespace App\Http\Controllers;

use App\Services\AuditLogger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReportController extends Controller
{
    protected AuditLogger $auditLogger;

    public function __construct(AuditLogger $auditLogger)
    {
        $this->auditLogger = $auditLogger;
    }

    public function profit(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $start = !empty($validated['start_date'])
            ? Carbon::parse($validated['start_date'])->startOfDay()
            : Carbon::now()->startOfMonth();

        $end = !empty($validated['end_date'])
            ? Carbon::parse($validated['end_date'])->endOfDay()
            : Carbon::now()->endOfMonth();

        if ($start->gt($end)) {
            throw ValidationException::withMessages([
                'end_date' => ['The end date must be on or after the start date.'],
            ]);
        }

        // 1. Revenue from sales payments.
        $revenue = DB::table('payments')
            ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->where('invoices.invoice_type', 'sales')
            ->whereBetween('payments.payment_date', [$start, $end])
            ->sum('payments.amount');

        // 2. Total expenses.
        $expenses = DB::table('expenses')
            ->whereBetween('expense_date', [$start, $end])
            ->sum('amount');

        // 3. Monthly sales breakdown.
        $monthlySales = DB::table('payments')
            ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->where('invoices.invoice_type', 'sales')
            ->whereBetween('payments.payment_date', [$start, $end])
            ->selectRaw("DATE_FORMAT(payments.payment_date, '%Y-%m') as month")
            ->selectRaw('SUM(payments.amount) as sales_total')
            ->groupByRaw("DATE_FORMAT(payments.payment_date, '%Y-%m')")
            ->orderBy('month')
            ->get();

        // Monthly expenses.
        $monthlyExpenses = DB::table('expenses')
            ->whereBetween('expense_date', [$start, $end])
            ->selectRaw("DATE_FORMAT(expense_date, '%Y-%m') as month")
            ->selectRaw('SUM(amount) as expense_total')
            ->groupByRaw("DATE_FORMAT(expense_date, '%Y-%m')")
            ->orderBy('month')
            ->get();

        // Merge sales and expenses by month.
        $monthlyData = [];

        foreach ($monthlySales as $month) {
            $monthlyData[$month->month] = [
                'month' => $month->month,
                'sales' => $month->sales_total,
                'expenses' => 0,
                'profit' => $month->sales_total,
            ];
        }

        foreach ($monthlyExpenses as $month) {
            if (!isset($monthlyData[$month->month])) {
                $monthlyData[$month->month] = [
                    'month' => $month->month,
                    'sales' => 0,
                    'expenses' => $month->expense_total,
                    'profit' => -$month->expense_total,
                ];
            } else {
                $monthlyData[$month->month]['expenses'] = $month->expense_total;
                $monthlyData[$month->month]['profit'] =
                    $monthlyData[$month->month]['sales'] - $month->expense_total;
            }
        }

        ksort($monthlyData);
        $monthlyData = array_values($monthlyData);

        // 4. Sales details.
        $detailsSales = DB::table('payments')
            ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->where('invoices.invoice_type', 'sales')
            ->whereBetween('payments.payment_date', [$start, $end])
            ->select(
                'payments.id',
                'payments.amount',
                'payments.payment_date',
                DB::raw("'sale' as type"),
                'invoices.invoice_number as reference'
            )
            ->get()
            ->toArray();

        // Expense details.
        $detailsExpenses = DB::table('expenses')
            ->whereBetween('expense_date', [$start, $end])
            ->select(
                'expenses.id',
                'expenses.amount',
                'expenses.expense_date as payment_date',
                DB::raw("'expense' as type"),
                'expenses.description as reference'
            )
            ->get()
            ->toArray();

        $details = array_merge($detailsSales, $detailsExpenses);

        usort($details, function ($a, $b) {
            return strtotime($b->payment_date) <=> strtotime($a->payment_date);
        });

        // Audit report access without recording financial figures.
        $this->auditLogger->record(
            'report.profit_viewed',
            'Profit report retrieved',
            null,
            [
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
                'sales_records' => count($detailsSales),
                'expense_records' => count($detailsExpenses),
            ],
            $request,
            auth('api')->id()
        );

        return response()->json([
            'summary' => [
                'total_sales' => $revenue,
                'total_expenses' => $expenses,
                'gross_profit' => $revenue - $expenses,
                'net_profit' => $revenue - $expenses,
            ],
            'monthly' => $monthlyData,
            'details' => $details,
        ]);
    }
}