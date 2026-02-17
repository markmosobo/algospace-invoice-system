<?php

namespace App\Http\Controllers;

use App\Models\FarmVenture;
use App\Models\FarmWorker;
use App\Models\FarmWorkerTask;
use App\Models\SystemLog;
use Illuminate\Http\Request;

class FarmWorkerTaskController extends Controller
{
   /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $farmventures = FarmVenture::with('farm')->get();
        $farmworkertasks = FarmWorkerTask::get();

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' retrieved farmworker tasks'
        ]); 

        return response()->json([
            'farmventures' => $farmventures,
            'farmworkertasks' => $farmworkertasks
        ]);        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $farmworkertask = new FarmWorker();
        $farmworkertask->worker_id = $request->worker_id;
        $farmworkertask->venture_id = $request->venture_id;
        $farmworkertask->task = $request->task;
        $farmworkertask->work_date = $request->work_date;
        $farmworkertask->amount_paid = $request->amount_paid;
        $farmworkertask->save();

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' created farmworker task #'.$farmworkertask->id
        ]);          
                
        return response()->json($farmworkertask);        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $farmworkertask = FarmWorkerTask::find($id);
        return response()->json($farmworkertask);        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FarmWorker $farmworkertask)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $farmworkertask->update($request->only([
            'worker_id','venture_id','task','work_date','amount_paid'
        ]));

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' updated details for farmworker task #'.$farmworkertask->id
        ]);         

        return response()->json(['message' => 'Updated']);        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        FarmWorkerTask::destroy($id);

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' deleted farmworker task #'.$id
        ]);         
        return response()->json(['message' => 'Deleted']);        
    }}
