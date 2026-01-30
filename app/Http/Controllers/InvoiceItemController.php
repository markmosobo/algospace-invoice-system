<?php

namespace App\Http\Controllers;

use App\Models\InvoiceItem;
use App\Models\SystemLog;
use Illuminate\Http\Request;

class InvoiceItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $invoiceitems = InvoiceItem::get();

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' retrieved invoice items'
        ]);

        return response()->json($invoiceitems);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'service_id' => 'required|exists:services,id',
            'quantity'   => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
        ]);

        // Calculate total
        $total = $request->quantity * $request->unit_price;

        // Create new invoice item
        $invoiceItem = InvoiceItem::create([
            'invoice_id' => $request->invoice_id,
            'service_id' => $request->service_id,
            'quantity'   => $request->quantity,
            'unit_price' => $request->unit_price,
            'total'      => $total,
        ]);

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' created invoice item #'.$invoiceItem->id
        ]);        

        return response()->json([
            'message' => 'Invoice item created successfully',
            'invoice_item' => $invoiceItem
        ]);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $invoiceitem = InvoiceItem::find($id);
        return response()->json($invoiceitem);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Find the invoice item or fail with 404
        $invoiceItem = InvoiceItem::findOrFail($id);

        // Validate request
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'service_id' => 'required|exists:services,id',
            'quantity'   => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
        ]);

        // Recalculate total
        $total = $request->quantity * $request->unit_price;

        // Update invoice item
        $invoiceItem->update([
            'invoice_id' => $request->invoice_id,
            'service_id' => $request->service_id,
            'quantity'   => $request->quantity,
            'unit_price' => $request->unit_price,
            'total'      => $total,
        ]);

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' updated invoice item #'.$invoiceItem->id
        ]);        

        return response()->json([
            'message' => 'Invoice item updated successfully',
            'invoice_item' => $invoiceItem
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        InvoiceItem::destroy($id);

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' deleted invoice item #'.$id
        ]);

        return response()->json(['message' => 'Deleted']);
    }
}
