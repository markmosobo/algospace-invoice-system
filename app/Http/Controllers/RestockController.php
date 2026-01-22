<?php

namespace App\Http\Controllers;

use App\Models\Restock;
use App\Models\Supplier;
use App\Models\Supply;
use App\Models\SystemLog;
use Illuminate\Http\Request;

class RestockController extends Controller
{
    public function index()
    {
        $restocks = Restock::with('supplier', 'supply')->get();
        $supplies = Supply::with('supplier')->get();
        $suppliers = Supplier::get();

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' retrieved restocks'
        ]); 

        // Return as JSON
        return response()->json([
            'restocks' => $restocks,
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
            'supply_id'    => 'required|exists:supplies,id',
            'supplier_id'    => 'required|exists:suppliers,id',
            'buying_price'   => 'required|numeric|min:0',
            'quantity'   => 'required|numeric|min:0',
        ]);

        // Create new restock
        $restock = Restock::create([
            'supply_id'    => $request->supply_id,
            'supplier_id'   => $request->supplier_id,
            'buying_price'       => $request->buying_price,
            'status'         => $request->status ?? 'pending',
            'quantity'   => $request->quantity,
            'user_id' => auth('api')->user()->id
        ]);

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' created restock id '.$restock->id
        ]);         

        return response()->json([
            'message' => 'Restock created successfully',
            'restock' => $restock
        ]);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $restock = Restock::find($id);
        return response()->json($restock);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Find the restock
        $restock = Restock::findOrFail($id);

        // Validate the incoming request
        $request->validate([
            'supply_id'    => 'required|exists:supplies,id',
            'supplier_id'    => 'required|exists:suppliers,id',
            'buying_price'   => 'required|numeric|min:0',
            'quantity'   => 'required|numeric|min:0',
        ]);

        // Update invoice
        $restock->update([
            'supply_id'    => $request->supply_id,
            'supplier_id'   => $request->supplier_id,
            'buying_price'       => $request->buying_price,
            'quantity'   => $request->quantity,
            'user_id' => auth('api')->user()->id,
            'status'         => $request->status ?? $restock->status,
        ]);

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' updated restock id '.$restock->id
        ]);        

        return response()->json([
            'message' => 'Restock updated successfully',
            'restock' => $restock
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Restock::destroy($id);

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' deleted restock id '.$id
        ]); 

        return response()->json(['message' => 'Deleted']);
    }    
}
