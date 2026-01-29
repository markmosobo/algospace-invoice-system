<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\DiaryEntry;
use App\Models\Payment;
use App\Models\Service;
use App\Models\SystemLog;
use Illuminate\Http\Request;

class ListController extends Controller
{
    public function quickSales()
    {
        $sales = Payment::with(['invoice.customer'])
            ->whereHas('invoice', function ($q) {
                $q->where('invoice_type', 'sales');
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


        // Load customers for wizard dropdown
        $customers = Customer::select('id', 'name','phone')->get();
        $services = Service::get();

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' retrieved quick sales'
        ]);        

        return response()->json([
            'quickSales' => $sales,
            'customers' => $customers,
            'services' => $services
        ]);
    }

    
}
