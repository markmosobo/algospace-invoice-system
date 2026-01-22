<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payments = Payment::with('invoice')->get();
        $invoices = Invoice::with('customer')->where('status', 'pending')->get();
        // Return as JSON
        return response()->json([
            'payments' => $payments,
            'invoices' => $invoices,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'invoice_id'   => 'required|exists:invoices,id',
            'amount'       => 'required|numeric|min:1',
            'payment_date' => 'required|date',
            'method'       => 'required|in:cash,mpesa,bank',
            'mpesa_code'   => 'required_if:method,mpesa|string|max:20',
        ]);

        $payment = Payment::create([
            'invoice_id'   => $request->invoice_id,
            'amount'       => $request->amount,
            'payment_date' => $request->payment_date,
            'method'       => $request->method,
            'mpesa_code'   => $request->mpesa_code ?? null,
        ]);

        return response()->json([
            'message' => 'Payment recorded successfully',
            'payment' => $payment
        ]);
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $payment = Payment::find($id);
        return response()->json($payment);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $payment = Payment::findOrFail($id);

        $request->validate([
            'invoice_id'   => 'required|exists:invoices,id',
            'amount'       => 'required|numeric|min:0.01',
            'payment_date' => 'nullable|date',
            'method'       => 'required|in:cash,mpesa,bank',
            'mpesa_code'   => 'required_if:method,mpesa|string|max:20',
        ]);

        $payment->update([
            'invoice_id'   => $request->invoice_id,
            'amount'       => $request->amount,
            'payment_date' => $request->payment_date ?? $payment->payment_date,
            'method'       => $request->method ?? $payment->method,
            'mpesa_code'   => $request->mpesa_code ?? $payment->mpesa_code,
        ]);

        return response()->json([
            'message' => 'Payment updated successfully',
            'payment' => $payment
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Payment::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }
}
