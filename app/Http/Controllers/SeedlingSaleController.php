<?php

namespace App\Http\Controllers;

use App\Models\Seedling;
use App\Models\SeedlingSale;
use App\Models\SystemLog;
use Illuminate\Http\Request;

class SeedlingSaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $seedlingsales = SeedlingSale::with('seedling')->get();
        $seedlings = Seedling::with('venture')->get();

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' retrieved seedling sales'
        ]); 

        return response()->json([
            'seedlingsales' => $seedlingsales,
            'seedlings' => $seedlings
        ]);          
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->merge([
            'total_amount' => $request->quantity_sold * $request->price_per_unit
        ]);

        $seedlingsale = SeedlingSale::create($request->only([
            'seedling_id',
            'buyer_name',
            'quantity_sold',
            'price_per_unit',
            'sale_date',
            'total_amount'
        ]));

        SystemLog::create([
            'user_id'     => auth('api')->user()->id,
            'description' => auth('api')->user()->name . ' created seedling sale #' . $seedlingsale->id,
        ]);

        return response()->json($seedlingsale, 201);
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $seedlingsale = SeedlingSale::find($id);
        return response()->json($seedlingsale);         
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SeedlingSale $seedlingsale)
    {
        // Validate incoming request
        $seedlingsale = SeedlingSale::create($request->only([
            'seedling_id',
            'buyer_name',
            'quantity_sold',
            'price_per_unit',
            'sale_date',
            'total_amount'
        ]));

        $seedlingsale->update($request->only([
            'seedling_id','buyer_name','quantity_sold','price_per_unit','sale_date',
            'total_amount'
        ]));

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' updated details for seedling sale #'.$seedlingsale->id
        ]);         

        return response()->json(['message' => 'Updated']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        SeedlingSale::destroy($id);

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' deleted seedling sale #'.$id
        ]);         
        return response()->json(['message' => 'Deleted']);         
    }
}
