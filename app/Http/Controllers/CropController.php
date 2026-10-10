<?php

namespace App\Http\Controllers;

use App\Models\Crop;
use App\Models\FarmVenture;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

class CropController extends Controller
{
    /**
     * Display crops and farm ventures.
     */
    public function index()
    {
        $farmventures = FarmVenture::with('farm')->get();
        $crops = Crop::get();

        return response()->json([
            'farmventures' => $farmventures,
            'crops' => $crops,
        ]);
    }

    /**
     * Store a newly created crop.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'venture_id' => 'required|exists:farm_ventures,id',
            'crop_name' => 'required|string|max:255',
            'variety' => 'nullable|string|max:255',
            'planting_date' => 'nullable|date',
            'expected_harvest_date' => 'nullable|date|after_or_equal:planting_date',
            'acreage' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:active,inactive,dormant',
        ]);

        $crop = new Crop();
        $crop->venture_id = $data['venture_id'];
        $crop->crop_name = $data['crop_name'];
        $crop->variety = $data['variety'] ?? null;
        $crop->planting_date = $data['planting_date'] ?? null;
        $crop->expected_harvest_date = $data['expected_harvest_date'] ?? null;
        $crop->acreage = $data['acreage'] ?? null;
        $crop->status = $data['status'] ?? 'active';
        $crop->save();

        app(AuditLogger::class)->record(
            'crop.created',
            'Crop created: ' . $crop->crop_name,
            $crop,
            [
                'crop_id' => $crop->id,
                'venture_id' => $crop->venture_id,
                'crop_name' => $crop->crop_name,
                'variety' => $crop->variety,
                'planting_date' => $crop->planting_date,
                'expected_harvest_date' => $crop->expected_harvest_date,
                'acreage' => $crop->acreage,
                'status' => $crop->status,
            ],
            $request
        );

        return response()->json($crop, 201);
    }

    /**
     * Display a specific crop.
     */
    public function show(string $id)
    {
        $crop = Crop::find($id);

        return response()->json($crop);
    }

    /**
     * Update a crop.
     */
    public function update(Request $request, Crop $crop)
    {
        $data = $request->validate([
            'venture_id' => 'required|exists:farm_ventures,id',
            'crop_name' => 'required|string|max:255',
            'variety' => 'nullable|string|max:255',
            'planting_date' => 'nullable|date',
            'expected_harvest_date' => 'nullable|date|after_or_equal:planting_date',
            'acreage' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:active,inactive,dormant',
        ]);

        $fields = [
            'venture_id',
            'crop_name',
            'variety',
            'planting_date',
            'expected_harvest_date',
            'acreage',
            'status',
        ];

        $before = $crop->only($fields);

        $crop->update($request->only($fields));
        $crop->refresh();

        $after = $crop->only($fields);
        $changes = [];

        foreach ($after as $field => $value) {
            if (($before[$field] ?? null) != $value) {
                $changes[$field] = [
                    'old' => $before[$field] ?? null,
                    'new' => $value,
                ];
            }
        }

        if (!empty($changes)) {
            app(AuditLogger::class)->record(
                'crop.updated',
                'Crop updated: ' . $crop->crop_name,
                $crop,
                [
                    'crop_id' => $crop->id,
                    'changes' => $changes,
                ],
                $request
            );
        }

        return response()->json([
            'message' => 'Updated',
        ]);
    }

    /**
     * Delete a crop.
     */
    public function destroy(Request $request, string $id)
    {
        $crop = Crop::find($id);

        if (!$crop) {
            return response()->json([
                'message' => 'Crop not found',
            ], 404);
        }

        app(AuditLogger::class)->record(
            'crop.deleted',
            'Crop deleted: ' . $crop->crop_name,
            $crop,
            [
                'crop_id' => $crop->id,
                'venture_id' => $crop->venture_id,
                'crop_name' => $crop->crop_name,
                'variety' => $crop->variety,
                'planting_date' => $crop->planting_date,
                'expected_harvest_date' => $crop->expected_harvest_date,
                'acreage' => $crop->acreage,
                'status' => $crop->status,
            ],
            $request
        );

        $crop->delete();

        return response()->json([
            'message' => 'Deleted',
        ]);
    }
}