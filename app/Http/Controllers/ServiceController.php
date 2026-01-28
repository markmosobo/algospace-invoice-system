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
        $request->validate([
            'name'      => 'required|string|max:255',
            'category'  => 'required|string|max:255',
            'price'     => 'required|numeric|min:0',
            'unit'      => 'required|string|max:50',
            'is_bundle' => 'sometimes|boolean',
        ]);

        $service = Service::create([
            'name'      => $request->name,
            'category'  => $request->category,
            'price'     => $request->price,
            'unit'      => $request->unit,
            'is_bundle' => $request->is_bundle ?? false,
        ]);

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
        // Find the service or fail
        $service = Service::findOrFail($id);

        // Validate request
        $request->validate([
            'name'      => 'required|string|max:255',
            'category'  => 'required|string|max:255',
            'price'     => 'required|numeric|min:0',
            'unit'      => 'required|string|max:50',
            'is_bundle' => 'sometimes|boolean',
        ]);

        // Update service
        $service->update([
            'name'      => $request->name,
            'category'  => $request->category,
            'price'     => $request->price,
            'unit'      => $request->unit,
            'is_bundle' => $request->is_bundle ?? $service->is_bundle,
        ]);

        // System log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' =>
                auth('api')->user()->name.' updated service id '.$service->id
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
