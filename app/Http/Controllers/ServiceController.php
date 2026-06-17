<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\SystemLog;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

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
            'type' => 'sometimes|in:service,course',
            'tier' => 'nullable|string|max:50',
            'duration_days' => 'nullable|integer|min:1',
            'is_bundle' => 'sometimes|boolean',
        ]);

        $service = Service::create([
            'name'      => $request->name,
            'category'  => $request->category,
            'price'     => $request->price,
            'unit'      => $request->unit,
            'type'      => $request->type ?? 'service',
            'tier'      => $request->tier ?? null,
            'duration_days' => $request->duration_days ?? null,
            'is_bundle' => $request->is_bundle ?? false,
        ]);

        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' created service #'.$service->id
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
            'type' => 'sometimes|in:service,course',
            'tier' => 'nullable|string|max:50',
            'duration_days' => 'nullable|integer|min:1',
            'is_bundle' => 'sometimes|boolean',
        ]);

        // Update service
        $service->update([
            'name'      => $request->name,
            'category'  => $request->category,
            'price'     => $request->price,
            'unit'      => $request->unit,
            'type' => $request->type ?? $service->type,
            'tier' => $request->tier ?? $service->tier,
            'duration_days' => $request->duration_days ?? $service->duration_days,
            'is_bundle' => $request->is_bundle ?? $service->is_bundle,
        ]);

        // System log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' =>
                auth('api')->user()->name.' updated service #'.$service->id
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
            'description' => auth('api')->user()->name.' deleted service #'.$id
        ]); 

        return response()->json(['message' => 'Deleted']);
    }

    // ServiceController.php
    public function toggleActive($id)
    {
        $service = Service::findOrFail($id);

        $service->is_active = !$service->is_active;
        $service->save();

        return response()->json([
            'message' => 'Service status updated',
            'is_active' => $service->is_active
        ]);
    }
    

    public function exportPdf()
    {
        $services = Service::all();
        // dd($services);

        $grouped = $services->groupBy(function ($service) {
            return $service->category ?? 'Uncategorized';
        });

        $data = [
            'grouped' => $grouped,
            'printDate' => now()->format('d/m/Y'),
        ];

        $pdf = Pdf::loadView('pdf.services', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->download('ALGOSPACE_SERVICES.pdf');
    } 
    
    public function courses()
    {
        $courses = Service::where('category', 'Training')
            ->where('is_active', 1)
            ->select('id', 'name', 'price', 'unit', 'category')
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => $courses
        ]);
    }    
}
