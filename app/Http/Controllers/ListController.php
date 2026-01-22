<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Payment;
use Illuminate\Http\Request;

class ListController extends Controller
{
    public function quickSales()
    {
        $sales = Payment::with(['invoice.customer'])
            ->orderBy('payment_date', 'desc')
            ->get()
            ->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'invoice_no' => $payment->invoice->invoice_no,
                    'customer_name' => $payment->invoice->customer->name,
                    'amount' => $payment->amount,
                    'method' => $payment->method,
                    'payment_date' => $payment->payment_date,
                    'status' => $payment->invoice->status,
                ];
            });

        // Load customers for wizard dropdown
        $customers = Customer::select('id', 'name','phone')->get();

        return response()->json([
            'quickSales' => $sales,
            'customers' => $customers
        ]);
    }
    
}
