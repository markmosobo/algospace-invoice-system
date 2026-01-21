<?php

namespace App\Http\Controllers;

use App\Models\Restock;
use App\Models\Supplier;
use App\Models\Supply;
use Illuminate\Http\Request;

class SupplyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $supplies = Supply::with('supplier')->get();
        $suppliers = Supplier::get();
        // Return as JSON
        return response()->json([
            'supplies' => $supplies,
            'suppliers' => $suppliers,
        ]);         
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

    public function restock(Request $request)
{
    $request->validate([
        'supply_id' => 'required|integer|exists:supplies,id',
        'quantity' => 'required|integer|min:1',
        'buying_price' => 'required|numeric',
        'supplier_id' => 'required|integer|exists:suppliers,id',
    ]);

    $product = Supply::find($request->supply_id);

    // 1️⃣ Update product quantity
    $product->quantity += $request->quantity;
    $product->save();

    // 2️⃣ Save restock history
    Restock::create([
        'supply_id' => $request->supply_id,
        'quantity' => $request->quantity,
        'buying_price' => $request->buying_price,
        'supplier_id' => $request->supplier_id,
        'user_id' => auth('api')->id(),
    ]);

    return response()->json([
        'status' => 'success',
        'message' => 'Product restocked successfully'
    ]);
    }

    }
