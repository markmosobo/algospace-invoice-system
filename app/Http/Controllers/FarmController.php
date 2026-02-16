<?php

namespace App\Http\Controllers;

use App\Models\Farm;
use App\Models\SystemLog;
use Illuminate\Http\Request;

class FarmController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $farms = Farm::get();

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' retrieved farms'
        ]); 
                 
        return response()->json($farms);        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $farm = new Farm();
        $farm->name = $request->name;
        $farm->location = $request->location;
        $farm->size = $request->size;
        $farm->description = $request->description;
        $farm->owner_id = auth('api')->user()->id;
        $farm->save();

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' created farm #'.$farm->id
        ]);          
                
        return response()->json($farm);        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $farm = Farm::find($id);
        return response()->json($farm);        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Farm $farm)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $farm->update($request->only([
            'name','location','size','description'
        ]));

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' updated details for farm #'.$farm->id
        ]);         

        return response()->json(['message' => 'Updated']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Farm::destroy($id);

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' deleted farm #'.$id
        ]);         
        return response()->json(['message' => 'Deleted']);        
    }
}
