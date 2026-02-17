<?php

namespace App\Http\Controllers;

use App\Models\FarmInput;
use App\Models\FarmVenture;
use App\Models\SystemLog;
use Illuminate\Http\Request;

class FarmInputController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $farmventures = FarmVenture::with('farm')->get();
        $farminputs = FarmInput::with('venture')->get();

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' retrieved farm inputs'
        ]); 

        return response()->json([
            'farmventures' => $farmventures,
            'farminputs' => $farminputs
        ]);        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Create expense using mass assignment
        $farminput = FarmInput::create($request->only([
            'venture_id',
            'input_name',
            'input_type',
            'quantity',
            'expense_date',
            'unit',
            'date_applied'
        ]));

        // Record system log
        SystemLog::create([
            'user_id'     => auth('api')->user()->id,
            'description' => auth('api')->user()->name . ' created farm input #' . $farminput->id,
        ]);

        return response()->json($farminput, 201); // return 201 Created         
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $farminput = FarmInput::find($id);
        return response()->json($farminput);        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FarmInput $farminput)
    {
        $farminput->update($request->only([
            'venture_id','input_name','input_type','quantity','unit',
            'date_applied'
        ]));

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' updated details for farm input #'.$farminput->id
        ]);         

        return response()->json(['message' => 'Updated']);        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        FarmInput::destroy($id);

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' deleted farm input #'.$id
        ]);         
        return response()->json(['message' => 'Deleted']);        
    }
}
