<?php

namespace App\Http\Controllers;

use App\Models\Crop;
use App\Models\FarmVenture;
use App\Models\SystemLog;
use Illuminate\Http\Request;

class CropController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $farmventures = FarmVenture::with('farm')->get();
        $crops = Crop::get();

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' retrieved crops'
        ]); 

        return response()->json([
            'farmventures' => $farmventures,
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
            'venture_id'           => 'required|exists:farm_ventures,id',
            'crop_name'            => 'required|string|max:255',
            'variety'              => 'nullable|string|max:255',
            'planting_date'        => 'nullable|date',
            'expected_harvest_date'=> 'nullable|date|after_or_equal:planting_date',
            'acreage'              => 'nullable|numeric|min:0',
            'status'               => 'nullable|in:active,inactive,dormant',
        ]);

        // Create crop
        $crop = new Crop();
        $crop->venture_id = $request->venture_id;
        $crop->crop_name = $request->crop_name;
        $crop->variety = $request->variety;
        $crop->planting_date = $request->planting_date;
        $crop->expected_harvest_date = $request->expected_harvest_date;
        $crop->acreage = $request->acreage;
        $crop->status = $request->status ?? 'active'; // default to active if not provided
        $crop->save();

        // Record system log
        SystemLog::create([
            'user_id'     => auth('api')->user()->id,
            'description' => auth('api')->user()->name . ' created crop #' . $crop->id,
        ]);

        return response()->json($crop, 201); // return 201 Created
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $crop = Crop::find($id);
        return response()->json($crop);        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Crop $crop)
    {
        // Validate incoming request
        $request->validate([
            'venture_id'           => 'required|exists:farm_ventures,id',
            'crop_name'            => 'required|string|max:255',
            'variety'              => 'nullable|string|max:255',
            'planting_date'        => 'nullable|date',
            'expected_harvest_date'=> 'nullable|date|after_or_equal:planting_date',
            'acreage'              => 'nullable|numeric|min:0',
            'status'               => 'nullable|in:active,inactive,dormant',
        ]);

        $crop->update($request->only([
            'venture_id','crop_name','variety','planting_date','expected_harvest_date',
            'acreage','status'
        ]));

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' updated details for crop #'.$crop->id
        ]);         

        return response()->json(['message' => 'Updated']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Crop::destroy($id);

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' deleted crop #'.$id
        ]);         
        return response()->json(['message' => 'Deleted']);         
    }
}
