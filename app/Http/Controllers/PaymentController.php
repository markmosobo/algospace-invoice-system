<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\SystemLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payments = Payment::with('invoice')->get();
        $invoices = Invoice::with('customer')->where('status', 'pending')->get();

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' retrieved payments'
        ]);

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
            'mpesa_code' => 'nullable|required_if:method,mpesa|string|max:20',
        ]);

        $invoice = Invoice::find($request->invoice_id);

        // create payment
        $payment = Payment::create([
            'invoice_id'   => $request->invoice_id,
            'amount'       => $request->amount,
            'payment_date' => $request->payment_date,
            'method'       => $request->method,
            'mpesa_code'   => $request->mpesa_code ?? null,
            'comment'      => $request->comment ?? null,
        ]);

        // ✅ Update invoice status if paid
        if ($invoice->total_amount <= $request->amount) {
            $invoice->update(['status' => 'paid']);
        }

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' created payment id '.$payment->id
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
            'mpesa_code'   => 'sometimes|required_if:method,mpesa|string|max:20',
        ]);

        $payment->update([
            'invoice_id'   => $request->invoice_id,
            'amount'       => $request->amount,
            'payment_date' => $request->payment_date ?? $payment->payment_date,
            'method'       => $request->method ?? $payment->method,
            'mpesa_code'   => $request->mpesa_code ?? $payment->mpesa_code,
            'comment'   => $request->comment,
        ]);

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' updated payment id '.$payment->id
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

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' deleted payment id '.$id
        ]); 

        return response()->json(['message' => 'Deleted']);
    }

    public function destroySale($id)
    {
        DB::transaction(function () use ($id) {

            $payment = Payment::findOrFail($id);
            $invoice = $payment->invoice;

            $payment->delete();

            if ($invoice) {
                $invoice->delete();
            }

        });

        return response()->json(['message' => 'Sale deleted successfully']);
    }

    public function complete($id)
    {
        $payment = Payment::findOrFail($id);
        $invoice = $payment->invoice;

        $totalPaid = $invoice->payments()->sum('amount');
        $balance = $invoice->total_amount - $totalPaid;

        if ($balance > 0) {
            Payment::create([
                'invoice_id'   => $invoice->id,
                'amount'       => $balance,
                'payment_date' => now(),
                'method'       => $payment->method,
                'mpesa_code'   => $payment->mpesa_code,
                'comment'      => 'Balance cleared',
            ]);
        }

        $invoice->update(['status' => 'paid']);

        return response()->json([
            'message' => 'Invoice fully paid',
            'invoice_status' => 'paid'
        ]);
    }


    public function showSale($id)
    {
        $payment = Payment::with([
            'invoice.items',
            'invoice.customer'
        ])->findOrFail($id);

        $invoice = $payment->invoice;

        return response()->json([
            'invoice_no'     => $invoice->invoice_number,
            'customer_name'  => $invoice->customer->name,
            'items'          => $invoice->items,
            'invoice_total'  => $invoice->total_amount,
            'total_paid'     => $invoice->payments()->sum('amount'),
            'status'         => $invoice->status,
            'method'         => $payment->method,
            'payment_date'   => $payment->payment_date,
            'mpesa_code'     => $payment->mpesa_code,
            'comment'        => $payment->comment,
        ]);
    }

    public function updateSale(Request $request, $id)
{
    // 1. Validate input
    $validated = $request->validate([
        'amount'       => 'required|numeric|min:1',
        'method'       => 'required|in:cash,mpesa,bank',
        'payment_date' => 'required|date',
        'mpesa_code'   => 'nullable|string',
    ]);

    // 2. Find payment
    $payment = Payment::findOrFail($id);

    // 3. Update payment
    $payment->update($validated);

    // 4. Get related invoice
    $invoice = $payment->invoice;

    // 5. Recalculate total paid
    $totalPaid = $invoice->payments()->sum('amount');

    // 6. Flip invoice status correctly
    $invoice->update([
        'status' => $totalPaid >= $invoice->total_amount ? 'paid' : 'pending'
    ]);

    return response()->json([
        'message' => 'Payment updated successfully',
        'invoice_status' => $invoice->status
    ]);
}





}
