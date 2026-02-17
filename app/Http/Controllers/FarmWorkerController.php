<?php

namespace App\Http\Controllers;

use App\Models\FarmWorker;
use App\Models\SystemLog;
use Illuminate\Http\Request;

class FarmWorkerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $farmworkers = FarmWorker::get();

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' retrieved farm workers'
        ]); 
                 
        return response()->json($farmworkers);        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $farmworker = new FarmWorker();
        $farmworker->name = $request->name;
        $farmworker->phone = $request->phone;
        $farmworker->role = $request->role;
        $farmworker->daily_rate = $request->daily_rate;
        $farmworker->status = $request->status;
        $farmworker->save();

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' created farmworker #'.$farmworker->id
        ]);          
                
        return response()->json($farmworker);        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $farmworker = FarmWorker::find($id);
        return response()->json($farmworker);        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FarmWorker $farmworker)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $farmworker->update($request->only([
            'name','email','phone','role','daily_rate','status'
        ]));

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' updated details for farmworker #'.$farmworker->id
        ]);         

        return response()->json(['message' => 'Updated']);        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        FarmWorker::destroy($id);

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' deleted farmworker #'.$id
        ]);         
        return response()->json(['message' => 'Deleted']);        
    }
}
