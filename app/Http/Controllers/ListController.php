<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\FootTraffic;
use App\Models\Payment;
use App\Models\Service;
use App\Services\AuditLogger;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ListController extends Controller
{
    public function quickSales(Request $request)
    {
        $sales = Payment::with(['invoice.customer'])
            ->whereHas('invoice', function ($query) {
                $query->where('invoice_type', 'sales');
            })
            ->orderBy('payment_date', 'desc')
            ->get()
            ->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'invoice_no' => $payment->invoice->invoice_number,
                    'customer_name' => $payment->invoice->customer->name,
                    'amount' => $payment->amount,
                    'method' => $payment->method,
                    'payment_date' => $payment->payment_date,
                    'status' => $payment->invoice->status,
                ];
            });

        // Load customers for the quick-sales wizard.
        $customers = Customer::with(['activeCard', 'loyaltyCards'])
            ->withCount('visits')
            ->get();

        $todayDate = Carbon::today();

        $todayFootTraffic = FootTraffic::whereDate(
            'created_at',
            $todayDate
        )->count();

        $services = Service::get();

        app(AuditLogger::class)->record(
            'quick_sales.list_viewed',
            'Quick sales data retrieved',
            null,
            [
                'sales_count' => $sales->count(),
                'customers_count' => $customers->count(),
                'services_count' => $services->count(),
                'today_foot_traffic' => $todayFootTraffic,
            ],
            $request,
            auth('api')->id()
        );

        return response()->json([
            'quickSales' => $sales,
            'customers' => $customers,
            'services' => $services,
            'todayfoottraffic' => $todayFootTraffic,
        ]);
    }
}