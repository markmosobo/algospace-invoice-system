<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\SystemLog;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pendinginvoices = Invoice::with('customer')
            ->where('status', 'pending')
            ->get()
            ->map(function($invoice) {
                $invoice->is_overdue = $invoice->due_date < now();
                return $invoice;
            });
        $invoices = Invoice::with('customer')->where('status', 'paid')->get();
        $customers = Customer::get();

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' retrieved invoices'
        ]);

        // Return as JSON
        return response()->json([
            'pendinginvoices' => $pendinginvoices,
            'invoices' => $invoices,
            'customers' => $customers,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'customer_id'    => 'required|exists:customers,id',
            // 'invoice_date'   => 'required|date',
            'due_date'       => 'nullable|date',
            'status'         => 'nullable|in:pending,paid,overdue',
            'total_amount'   => 'required|numeric|min:0',
        ]);

        // Create new invoice
        $invoice = Invoice::create([
            'customer_id'    => $request->customer_id,
            'invoice_number' => Invoice::generateInvoiceNumber(),
            'invoice_date'   => $request->invoice_date ?? now(),
            'due_date'       => $request->due_date,
            'status'         => $request->status ?? 'pending',
            'total_amount'   => $request->total_amount,
        ]);

        // Save items
        foreach ($request->items as $item) {
            InvoiceItem::create([
                'invoice_id'   => $invoice->id,
                'service_id'   => $item['service_id'],
                'service_name' => $item['name'],
                'unit_price'   => $item['price'],
                'quantity'     => $item['quantity'],
                'line_total'   => $item['line_total'],
            ]);
        }        

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' created invoice id '.$invoice->id
        ]);        

        return response()->json([
            'message' => 'Invoice created successfully',
            'invoice' => $invoice
        ]);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $invoice = Invoice::find($id);
        return response()->json($invoice);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Find the invoice
        $invoice = Invoice::findOrFail($id);

        // Validate incoming request
        $request->validate([
            'customer_id'    => 'required|exists:customers,id',
            'invoice_number' => 'required|unique:invoices,invoice_number,' . $id, // Ignore current invoice
            'invoice_date'   => 'required|date',
            'due_date'       => 'nullable|date',
            'status'         => 'nullable|in:pending,paid,overdue',
            'total_amount'   => 'required|numeric|min:0',
        ]);

        // Update invoice
        $invoice->update([
            'customer_id'    => $request->customer_id,
            'invoice_number' => $request->invoice_number,
            'invoice_date'   => $request->invoice_date,
            'due_date'       => $request->due_date,
            'status'         => $request->status ?? $invoice->status,
            'total_amount'   => $request->total_amount,
        ]);

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' updated invoice id '.$invoice->id
        ]);        

        return response()->json([
            'message' => 'Invoice updated successfully',
            'invoice' => $invoice
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Invoice::destroy($id);

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' deleted invoice id '.$id
        ]);

        return response()->json(['message' => 'Deleted']);
    }
}
