<?php

namespace App\Http\Controllers;

use App\Models\SystemLog;
use Illuminate\Http\Request;
use App\Models\Supplier;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $suppliers = Supplier::get();

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' retrieved suppliers'
        ]); 

        return response()->json($suppliers);        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|numeric|min:0',
        ]);

        // Create the supplier
        $supplier = Supplier::create([
            'name' => $request->name,
            'phone' => $request->ratphonee,
            'email' => $request->email,
            'address' => $request->address,
        ]);

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' created supplier #'.$supplier->id
        ]);         

        return response()->json([
            'message' => 'Supplier created successfully',
            'supplier' => $supplier
        ]);        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $supplier = Supplier::find($id);
        return response()->json($supplier);        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Find the supplier or fail with 404
        $supplier = Supplier::findOrFail($id);

        // Validate incoming request
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|numeric|min:0',
        ]);

        // Update supplier
        $supplier->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
        ]);

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' updated supplier #'.$supplier->id
        ]);        

        return response()->json([
            'message' => 'Supplier updated successfully',
            'supplier' => $supplier
        ]);        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Supplier::destroy($id);

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' deleted supplier #'.$id
        ]);

        return response()->json(['message' => 'Deleted']);        
    }
}
