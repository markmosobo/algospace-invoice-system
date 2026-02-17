<?php

namespace App\Http\Controllers;

use App\Models\Crop;
use App\Models\Harvest;
use App\Models\SystemLog;
use Illuminate\Http\Request;

class HarvestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $harvests = Harvest::with('crop')->get();
        $crops = Crop::get();

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' retrieved harvests'
        ]); 

        return response()->json([
            'harvests' => $harvests,
            'crops' => $crops
        ]);        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'crop_id'       => 'required|exists:crops,id',
            'harvest_date'  => 'required|date',
            'quantity'      => 'required|numeric|min:0',
            'unit'          => 'required|string|max:50',
            'quality_grade' => 'nullable|string|max:50',
            'remarks'       => 'nullable|string|max:255',
        ]);

        // Create harvest using mass assignment
        $harvest = Harvest::create($request->only([
            'crop_id',
            'harvest_date',
            'quantity',
            'unit',
            'quality_grade',
            'remarks'
        ]));

        // Record system log
        SystemLog::create([
            'user_id'     => auth('api')->user()->id,
            'description' => auth('api')->user()->name . ' created harvest #' . $harvest->id,
        ]);

        return response()->json($harvest, 201); // return 201 Created
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $harvest = Harvest::find($id);
        return response()->json($harvest);        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Harvest $harvest)
    {
        // Validate incoming request
        $request->validate([
            'crop_id'       => 'required|exists:crops,id',
            'harvest_date'  => 'required|date',
            'quantity'      => 'required|numeric|min:0',
            'unit'          => 'required|string|max:50',
            'quality_grade' => 'nullable|string|max:50',
            'remarks'       => 'nullable|string|max:255',
        ]);

        $harvest->update($request->only([
            'crop_id','harvest_date','quantity','unit','quality_grade',
            'remarks'
        ]));

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' updated details for harvest #'.$harvest->id
        ]);         

        return response()->json(['message' => 'Updated']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Harvest::destroy($id);

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' deleted harvest #'.$id
        ]);         
        return response()->json(['message' => 'Deleted']);           
    }
}
