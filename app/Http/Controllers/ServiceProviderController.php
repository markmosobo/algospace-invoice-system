<?php

namespace App\Http\Controllers;

use App\Models\ProviderService;
use App\Models\ServiceProvider;
use App\Models\SystemLog;
use Illuminate\Http\Request;

class ServiceProviderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $serviceProviders = ServiceProvider::get();
        $providerServices = ProviderService::get();

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' retrieved service providers'
        ]);

        // Return as JSON
        return response()->json([
            'serviceProviders' => $serviceProviders,
            'providerServices' => $providerServices,
        ]);        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $serviceProvider = new ServiceProvider();
        $serviceProvider->name = $request->name;
        $serviceProvider->phone = $request->phone;
        $serviceProvider->email = $request->email;
        $serviceProvider->save();

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' created service provider #'.$serviceProvider->id
        ]);          
                
        return response()->json($serviceProvider);        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $serviceProvider = ServiceProvider::find($id);
        return response()->json($serviceProvider);        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ServiceProvider $serviceProvider)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $serviceProvider->update($request->only([
            'name','email','phone','gender'
        ]));

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' updated details for service provider #'.$serviceProvider->id
        ]);         

        return response()->json(['message' => 'Updated']);        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        ServiceProvider::destroy($id);

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' deleted service provider #'.$id
        ]);         
        return response()->json(['message' => 'Deleted']);        
    }
}
