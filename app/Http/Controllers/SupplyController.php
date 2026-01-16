<?php

namespace App\Http\Controllers;

use App\Models\Supply;
use Illuminate\Http\Request;

class SupplyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $supplies = Supply::get();
        return response()->json($supplies);         
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'quantity'   => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
        ]);

        // Calculate total
        $total = $request->quantity * $request->unit_price;

        // Create new invoice item
        $supply = Supply::create([
            'supplier_id' => $request->supplier_id,
            'item' => $request->item,
            'quantity'   => $request->quantity,
            'unit_price' => $request->unit_price,
            'total'      => $total,
            'payment_date'      => $request->payment_date,
            'method'      => $request->payment_method,
            'status'      => $request->status,
        ]);

        return response()->json([
            'message' => 'Supply created successfully',
            'supply' => $supply
        ]);        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $supply = Supply::find($id);
        return response()->json($supply);        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Find the invoice item or fail with 404
        $supply = Supply::findOrFail($id);

        // Validate request
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'quantity'   => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
        ]);

        // Recalculate total
        $total = $request->quantity * $request->unit_price;

        // Update supply item
        $supply->update([
            'supplier_id' => $request->supplier_id,
            'item' => $request->item,
            'quantity'   => $request->quantity,
            'unit_price' => $request->unit_price,
            'total'      => $total,
            'payment_date'      => $request->payment_date,
            'method'      => $request->payment_method,
            'status'      => $request->status,
        ]);

        return response()->json([
            'message' => 'Supply item updated successfully',
            'supply' => $supply
        ]);        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Supply::destroy($id);
        return response()->json(['message' => 'Deleted']);        
    }
}
