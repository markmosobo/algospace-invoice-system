<?php

namespace App\Http\Controllers;

use App\Models\FarmAsset;
use App\Models\SystemLog;
use Illuminate\Http\Request;

class FarmAssetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $farmassets = FarmAsset::get();

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' retrieved farm assets'
        ]); 
                 
        return response()->json($farmassets);         
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $farmasset = new FarmAsset();
        $farmasset->asset_name = $request->asset_name;
        $farmasset->asset_type = $request->asset_type;
        $farmasset->purchase_date = $request->purchase_date;
        $farmasset->cost = $request->cost;
        $farmasset->condition = $request->condition;
        $farmasset->notes = $request->notes;
        $farmasset->save();

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' created farm asset #'.$farmasset->id
        ]);          
                
        return response()->json($farmasset);        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $farmasset = FarmAsset::find($id);
        return response()->json($farmasset);        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FarmAsset $farmasset)
    {
        $request->validate([
            'asset_name' => 'required|string',
        ]);

        $farmasset->update($request->only([
            'asset_name','asset_type','purchase_date','cost','condition','notes'
        ]));

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' updated details for farm asset #'.$farmasset->id
        ]);         

        return response()->json(['message' => 'Updated']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        FarmAsset::destroy($id);

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' deleted farm asset #'.$id
        ]);         
        return response()->json(['message' => 'Deleted']);        
    }
}
