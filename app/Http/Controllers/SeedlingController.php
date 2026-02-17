<?php

namespace App\Http\Controllers;

use App\Models\FarmVenture;
use App\Models\Seedling;
use App\Models\SystemLog;
use Illuminate\Http\Request;

class SeedlingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $farmventures = FarmVenture::with('farm')->get();
        $seedlings = Seedling::with('venture')->get();

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' retrieved seedlings'
        ]); 

        return response()->json([
            'farmventures' => $farmventures,
            'seedlings' => $seedlings
        ]);         
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'venture_id'        => 'required|exists:farm_ventures,id',
            'seedling_type'     => 'required|string|max:100',
            'species_name'      => 'required|string|max:100',
            'date_planted'      => 'required|date',
            'quantity'          => 'required|integer|min:1',
            'expected_ready_date'=> 'nullable|date|after_or_equal:date_planted',
            'survival_rate'      => 'nullable|string|max:100',
        ]);

        // Create seedling using mass assignment
        $seedling = Seedling::create($request->only([
            'venture_id',
            'seedling_type',
            'species_name',
            'date_planted',
            'quantity',
            'expected_ready_date',
            'survival_rate'
        ]));

        // Record system log
        SystemLog::create([
            'user_id'     => auth('api')->user()->id,
            'description' => auth('api')->user()->name . ' created seedling #' . $seedling->id,
        ]);

        return response()->json($seedling, 201); // return 201 Created
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $seedling = Seedling::find($id);
        return response()->json($seedling);         
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Seedling $seedling)
    {
        // Validate incoming request
        $request->validate([
            'venture_id'        => 'required|exists:farm_ventures,id',
            'seedling_type'     => 'required|string|max:100',
            'species_name'      => 'required|string|max:100',
            'date_planted'      => 'required|date',
            'quantity'          => 'required|integer|min:1',
            'expected_ready_date'=> 'nullable|date|after_or_equal:date_planted',
            'survival_rate'      => 'nullable|string|max:100',
        ]);

        $seedling->update($request->only([
            'venture_id','seedling_type','species_name','date_planted','quantity',
            'expected_ready_date','survival_rate'
        ]));

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' updated details for seedling #'.$seedling->id
        ]);         

        return response()->json(['message' => 'Updated']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Seedling::destroy($id);

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' deleted seedling #'.$id
        ]);         
        return response()->json(['message' => 'Deleted']);          
    }
}
