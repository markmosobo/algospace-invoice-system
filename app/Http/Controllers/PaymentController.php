<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payments = Payment::get();
        return response()->json($payments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount'     => 'required|numeric|min:0.01',
            'payment_date' => 'nullable|date',
            'method'     => 'nullable|in:cash,mpesa,card,other',
        ]);

        // Create payment
        $payment = Payment::create([
            'invoice_id'   => $request->invoice_id,
            'amount'       => $request->amount,
            'payment_date' => $request->payment_date ?? now(),
            'method'       => $request->method ?? 'cash',
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
        // Find the payment or fail with 404
        $payment = Payment::findOrFail($id);

        // Validate incoming request
        $request->validate([
            'invoice_id'   => 'required|exists:invoices,id',
            'amount'       => 'required|numeric|min:0.01',
            'payment_date' => 'nullable|date',
            'method'       => 'nullable|in:cash,mpesa,card,other',
        ]);

        // Update the payment
        $payment->update([
            'invoice_id'   => $request->invoice_id,
            'amount'       => $request->amount,
            'payment_date' => $request->payment_date ?? $payment->payment_date,
            'method'       => $request->method ?? $payment->method,
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
