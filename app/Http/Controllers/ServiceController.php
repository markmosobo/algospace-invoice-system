<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\SystemLog;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $services = Service::get();

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' retrieved services'
        ]); 

        return response()->json($services);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
        ]);

        // Create the service
        $service = Service::create([
            'name' => $request->name,
            'rate' => $request->rate,
            'unit' => $request->unit,
        ]);

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' created service id '.$service->id
        ]);         

        return response()->json([
            'message' => 'Service created successfully',
            'service' => $service
        ]);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $service = Service::find($id);
        return response()->json($service);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Find the service or fail with 404
        $service = Service::findOrFail($id);

        // Validate incoming request
        $request->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
        ]);

        // Update service
        $service->update([
            'name' => $request->name,
            'rate' => $request->rate,
            'unit' => $request->unit,
        ]);

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' updated service id '.$service->id
        ]);         

        return response()->json([
            'message' => 'Service updated successfully',
            'service' => $service
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Service::destroy($id);

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' deleted service id '.$id
        ]); 

        return response()->json(['message' => 'Deleted']);
    }
}
