<?php

namespace App\Http\Controllers;

use App\Models\Crop;
use App\Models\Harvest;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

class HarvestController extends Controller
{
    /**
     * Display a listing of harvests and crops.
     */
    public function index()
    {
        $harvests = Harvest::with('crop')->get();
        $crops = Crop::get();

        return response()->json([
            'harvests' => $harvests,
            'crops' => $crops,
        ]);
    }

    /**
     * Store a newly created harvest.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'crop_id' => 'required|exists:crops,id',
            'harvest_date' => 'required|date',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'quality_grade' => 'nullable|string|max:50',
            'remarks' => 'nullable|string|max:255',
        ]);

        $harvest = Harvest::create($validated);

        app(AuditLogger::class)->record(
            'harvest.created',
            "Harvest created (ID: {$harvest->id})",
            $harvest,
            [
                'harvest_id' => $harvest->id,
                'crop_id' => $harvest->crop_id,
                'harvest_date' => $harvest->harvest_date,
                'quantity' => $harvest->quantity,
                'unit' => $harvest->unit,
                'quality_grade' => $harvest->quality_grade,
            ],
            $request,
            auth('api')->id()
        );

        return response()->json($harvest, 201);
    }

    /**
     * Display the specified harvest.
     */
    public function show(string $id)
    {
        $harvest = Harvest::with('crop')->find($id);

        if (!$harvest) {
            return response()->json([
                'message' => 'Harvest not found',
            ], 404);
        }

        return response()->json($harvest);
    }

    /**
     * Update the specified harvest.
     */
    public function update(Request $request, Harvest $harvest)
    {
        $validated = $request->validate([
            'crop_id' => 'required|exists:crops,id',
            'harvest_date' => 'required|date',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'quality_grade' => 'nullable|string|max:50',
            'remarks' => 'nullable|string|max:255',
        ]);

        $fields = [
            'crop_id',
            'harvest_date',
            'quantity',
            'unit',
            'quality_grade',
            'remarks',
        ];

        $before = $harvest->only($fields);

        $harvest->update($validated);
        $harvest->refresh();

        $changes = [];

        foreach ($fields as $field) {
            $oldValue = $before[$field] ?? null;
            $newValue = $harvest->getAttribute($field);

            if ($oldValue != $newValue) {
                $changes[$field] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        if (!empty($changes)) {
            app(AuditLogger::class)->record(
                'harvest.updated',
                "Harvest updated (ID: {$harvest->id})",
                $harvest,
                [
                    'harvest_id' => $harvest->id,
                    'changes' => $changes,
                ],
                $request,
                auth('api')->id()
            );
        }

        return response()->json([
            'message' => 'Updated',
        ]);
    }

    /**
     * Remove the specified harvest.
     */
    public function destroy(string $id)
    {
        $harvest = Harvest::find($id);

        if (!$harvest) {
            return response()->json([
                'message' => 'Harvest not found',
            ], 404);
        }

        $harvestId = $harvest->id;
        $cropId = $harvest->crop_id;
        $quantity = $harvest->quantity;
        $unit = $harvest->unit;
        $harvestDate = $harvest->harvest_date;

        $harvest->delete();

        app(AuditLogger::class)->record(
            'harvest.deleted',
            "Harvest deleted (ID: {$harvestId})",
            $harvest,
            [
                'harvest_id' => $harvestId,
                'crop_id' => $cropId,
                'quantity' => $quantity,
                'unit' => $unit,
                'harvest_date' => $harvestDate,
            ],
            request(),
            auth('api')->id()
        );

        return response()->json([
            'message' => 'Deleted',
        ]);
    }
}