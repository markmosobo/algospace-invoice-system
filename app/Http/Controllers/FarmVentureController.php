<?php

namespace App\Http\Controllers;

use App\Models\Farm;
use App\Models\FarmVenture;
use App\Models\SystemLog;
use Illuminate\Http\Request;

class FarmVentureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $farmventures = FarmVenture::with('farm')->get();
        $farms = Farm::get();

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' retrieved farm ventures'
        ]); 

        return response()->json([
            'farmventures' => $farmventures,
            'farms' => $farms
        ]);                 
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $farmventure = new FarmVenture();
        $farmventure->venture_name = $request->venture_name;
        $farmventure->venture_type = $request->venture_type;
        $farmventure->status = $request->status;
        $farmventure->farm_id = $request->farm_id;
        $farmventure->notes = $request->farm_id;
        $farmventure->start_date = now();
        $farmventure->save();

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' created farm venture #'.$farmventure->id
        ]);          
                
        return response()->json($farmventure);        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $farmventure = FarmVenture::find($id);
        return response()->json($farmventure);         
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FarmVenture $farmVenture)
    {
        $request->validate([
            'venture_name' => 'required|string',
        ]);

        $farmVenture->update($request->only([
            'venture_name','venture_type','status','notes','farm_id'
        ]));

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' updated details for farm venture #'.$farmVenture->id
        ]);         

        return response()->json(['message' => 'Updated']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        FarmVenture::destroy($id);

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' deleted farm venture #'.$id
        ]);         
        return response()->json(['message' => 'Deleted']);          
    }
}
