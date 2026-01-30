<?php

namespace App\Http\Controllers;

use App\Models\SystemLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function profit(Request $request)
    {
        $start = $request->start_date
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::now()->startOfMonth();

        $end = $request->end_date
            ? Carbon::parse($request->end_date)->endOfDay()
            : Carbon::now()->endOfMonth();


        // 1) Revenue from sales payments
        $revenue = DB::table('payments')
            ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->where('invoices.invoice_type', 'sales')
            ->whereBetween('payments.payment_date', [$start, $end])
            ->sum('payments.amount');


        // 2) Total expenses
        $expenses = DB::table('expenses')
            ->whereBetween('expense_date', [$start, $end])
            ->sum('amount');


        // -------------------------------
        // 3) Monthly profit breakdown
        // -------------------------------
        $monthly = DB::table('payments')
            ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->where('invoices.invoice_type', 'sales')
            ->whereBetween('payments.payment_date', [$start, $end])
            ->selectRaw("DATE_FORMAT(payments.payment_date, '%Y-%m') as month")
            ->selectRaw("SUM(payments.amount) as sales_total")
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->toArray();

        // Add expense totals by month
        $monthly_expenses = DB::table('expenses')
            ->whereBetween('expense_date', [$start, $end])
            ->selectRaw("DATE_FORMAT(expense_date, '%Y-%m') as month")
            ->selectRaw("SUM(amount) as expense_total")
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->toArray();

        // Merge sales + expenses into single monthly array
        $monthlyData = [];

        foreach ($monthly as $m) {
            $monthlyData[$m->month] = [
                'month' => $m->month,
                'sales' => $m->sales_total,
                'expenses' => 0,
                'profit' => $m->sales_total
            ];
        }

        foreach ($monthly_expenses as $e) {
            if (!isset($monthlyData[$e->month])) {
                $monthlyData[$e->month] = [
                    'month' => $e->month,
                    'sales' => 0,
                    'expenses' => $e->expense_total,
                    'profit' => -$e->expense_total
                ];
            } else {
                $monthlyData[$e->month]['expenses'] = $e->expense_total;
                $monthlyData[$e->month]['profit'] =
                    $monthlyData[$e->month]['sales'] - $e->expense_total;
            }
        }

        $monthlyData = array_values($monthlyData);


        // -------------------------------
        // 4) Details Table
        // -------------------------------
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

        usort($details, function($a, $b) {
            return strtotime($b->payment_date) - strtotime($a->payment_date);
        });

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' retrieved reports'
        ]);

        return response()->json([
            'summary' => [
                'total_sales'    => $revenue,
                'total_expenses' => $expenses,
                'gross_profit'   => $revenue - $expenses,
                'net_profit'     => $revenue - $expenses,
            ],
            'monthly' => $monthlyData,
            'details' => $details
        ]);
    }
    
}
