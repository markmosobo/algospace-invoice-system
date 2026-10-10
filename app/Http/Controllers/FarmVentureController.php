<?php

namespace App\Http\Controllers;

use App\Models\Farm;
use App\Models\FarmVenture;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

class FarmVentureController extends Controller
{
    /**
     * Display a listing of farm ventures.
     */
    public function index()
    {
        $farmventures = FarmVenture::with('farm')->get();
        $farms = Farm::get();

        return response()->json([
            'farmventures' => $farmventures,
            'farms' => $farms,
        ]);
    }

    /**
     * Store a newly created farm venture.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'venture_name' => 'required|string|max:255',
            'venture_type' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:100',
            'farm_id' => 'required|exists:farms,id',
            'notes' => 'nullable|string',
        ]);

        $farmventure = new FarmVenture();
        $farmventure->venture_name = $validated['venture_name'];
        $farmventure->venture_type = $validated['venture_type'] ?? null;
        $farmventure->status = $validated['status'] ?? null;
        $farmventure->farm_id = $validated['farm_id'];
        $farmventure->notes = $validated['notes'] ?? null;
        $farmventure->start_date = now();
        $farmventure->save();

        app(AuditLogger::class)->record(
            'farm_venture.created',
            "Farm venture created (ID: {$farmventure->id})",
            $farmventure,
            [
                'farm_venture_id' => $farmventure->id,
                'venture_name' => $farmventure->venture_name,
                'venture_type' => $farmventure->venture_type,
                'status' => $farmventure->status,
                'farm_id' => $farmventure->farm_id,
                'start_date' => $farmventure->start_date,
            ],
            $request,
            auth('api')->id()
        );

        return response()->json($farmventure);
    }

    /**
     * Display the specified farm venture.
     */
    public function show(string $id)
    {
        $farmventure = FarmVenture::find($id);

        return response()->json($farmventure);
    }

    /**
     * Update the specified farm venture.
     */
    public function update(Request $request, FarmVenture $farmVenture)
    {
        $validated = $request->validate([
            'venture_name' => 'required|string|max:255',
            'venture_type' => 'sometimes|nullable|string|max:255',
            'status' => 'sometimes|nullable|string|max:100',
            'notes' => 'sometimes|nullable|string',
            'farm_id' => 'sometimes|required|exists:farms,id',
        ]);

        $fields = [
            'venture_name',
            'venture_type',
            'status',
            'notes',
            'farm_id',
        ];

        $before = $farmVenture->only($fields);

        $farmVenture->update($validated);
        $farmVenture->refresh();

        $changes = [];

        foreach ($fields as $field) {
            $oldValue = $before[$field] ?? null;
            $newValue = $farmVenture->getAttribute($field);

            if ($oldValue != $newValue) {
                $changes[$field] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        if (!empty($changes)) {
            app(AuditLogger::class)->record(
                'farm_venture.updated',
                "Farm venture updated (ID: {$farmVenture->id})",
                $farmVenture,
                [
                    'farm_venture_id' => $farmVenture->id,
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
     * Remove the specified farm venture.
     */
    public function destroy(string $id)
    {
        $farmventure = FarmVenture::find($id);

        if (!$farmventure) {
            return response()->json([
                'message' => 'Farm venture not found',
            ], 404);
        }

        $ventureId = $farmventure->id;
        $ventureName = $farmventure->venture_name;
        $farmId = $farmventure->farm_id;
        $ventureType = $farmventure->venture_type;

        $farmventure->delete();

        app(AuditLogger::class)->record(
            'farm_venture.deleted',
            "Farm venture deleted (ID: {$ventureId})",
            $farmventure,
            [
                'farm_venture_id' => $ventureId,
                'venture_name' => $ventureName,
                'venture_type' => $ventureType,
                'farm_id' => $farmId,
            ],
            request(),
            auth('api')->id()
        );

        return response()->json([
            'message' => 'Deleted',
        ]);
    }
}