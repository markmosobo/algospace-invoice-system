<?php

namespace App\Http\Controllers;

use App\Models\FarmSale;
use App\Models\FarmVenture;
use App\Models\SystemLog;
use Illuminate\Http\Request;

class FarmSaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $farmventures = FarmVenture::with('farm')->get();
        $farmsales = FarmSale::with('venture')->get();

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' retrieved farm sales'
        ]); 

        return response()->json([
            'farmventures' => $farmventures,
            'farmsales' => $farmsales
        ]);        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->merge([
            'total_amount' => $request->quantity * $request->price_per_unit
        ]);

        // Create expense using mass assignment
        $farmsale = FarmSale::create($request->only([
            'venture_id',
            'product_name',
            'quantity',
            'unit',
            'price_per_unit',
            'buyer',
            'sale_date',
            'total_amount'
        ]));

        // Record system log
        SystemLog::create([
            'user_id'     => auth('api')->user()->id,
            'description' => auth('api')->user()->name . ' created farm sale #' . $farmsale->id,
        ]);

        return response()->json($farmsale, 201); // return 201 Created         
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $farmsale = FarmSale::find($id);
        return response()->json($farmsale);        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FarmSale $farmsale)
    {
        $farmsale->update($request->only([
            'venture_id','product_name','price_per_unit','quantity','unit',
            'buyer', 'sale_date', 'total_amount'
        ]));

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' updated details for farm sale #'.$farmsale->id
        ]);         

        return response()->json(['message' => 'Updated']);        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        FarmSale::destroy($id);

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' deleted farm sale #'.$id
        ]);         
        return response()->json(['message' => 'Deleted']);         
    }
}
