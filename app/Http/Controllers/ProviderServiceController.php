<?php

namespace App\Http\Controllers;

use App\Models\ProviderService;
use App\Models\SystemLog;
use Illuminate\Http\Request;

class ProviderServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $providerServices = ProviderService::get();

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' retrieved provider services'
        ]);

        // Return as JSON
        return response()->json([
            'providerServices' => $providerServices,
        ]);        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $providerService = new ProviderService();
        $providerService->name = $request->name;
        $providerService->category = $request->category;
        $providerService->price = $request->price;
        $providerService->save();

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' created provider service id '.$providerService->id
        ]);          
                
        return response()->json($providerService);          
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $providerService = ProviderService::find($id);
        return response()->json($providerService);         
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProviderService $providerService)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $providerService->update($request->only([
            'name','email','phone','gender'
        ]));

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' updated details for provider service id '.$providerService->id
        ]);         

        return response()->json(['message' => 'Updated']);        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        ProviderService::destroy($id);

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' deleted provider service id '.$id
        ]);         
        return response()->json(['message' => 'Deleted']);         
    }
}
