<?php

namespace App\Http\Controllers;

use App\Models\Farm;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

class FarmController extends Controller
{
    /**
     * Display a listing of farms.
     */
    public function index()
    {
        $farms = Farm::get();

        return response()->json($farms);
    }

    /**
     * Store a newly created farm.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'size' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $farm = new Farm();
        $farm->name = $validated['name'];
        $farm->location = $validated['location'] ?? null;
        $farm->size = $validated['size'] ?? null;
        $farm->description = $validated['description'] ?? null;
        $farm->owner_id = auth('api')->id();
        $farm->save();

        app(AuditLogger::class)->record(
            'farm.created',
            "Farm created (ID: {$farm->id})",
            $farm,
            [
                'farm_id' => $farm->id,
                'name' => $farm->name,
                'location' => $farm->location,
                'size' => $farm->size,
                'owner_id' => $farm->owner_id,
            ],
            $request,
            auth('api')->id()
        );

        return response()->json($farm);
    }

    /**
     * Display the specified farm.
     */
    public function show(string $id)
    {
        $farm = Farm::find($id);

        return response()->json($farm);
    }

    /**
     * Update the specified farm.
     */
    public function update(Request $request, Farm $farm)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'sometimes|nullable|string|max:255',
            'size' => 'sometimes|nullable|numeric|min:0',
            'description' => 'sometimes|nullable|string',
        ]);

        $fields = ['name', 'location', 'size', 'description'];
        $before = $farm->only($fields);

        $farm->update($validated);
        $farm->refresh();

        $changes = [];

        foreach ($fields as $field) {
            $oldValue = $before[$field] ?? null;
            $newValue = $farm->getAttribute($field);

            if ($oldValue != $newValue) {
                $changes[$field] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        if (!empty($changes)) {
            app(AuditLogger::class)->record(
                'farm.updated',
                "Farm updated (ID: {$farm->id})",
                $farm,
                [
                    'farm_id' => $farm->id,
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
     * Remove the specified farm.
     */
    public function destroy(string $id)
    {
        $farm = Farm::find($id);

        if (!$farm) {
            return response()->json([
                'message' => 'Farm not found',
            ], 404);
        }

        $farmId = $farm->id;
        $farmName = $farm->name;
        $ownerId = $farm->owner_id;

        $farm->delete();

        app(AuditLogger::class)->record(
            'farm.deleted',
            "Farm deleted (ID: {$farmId})",
            $farm,
            [
                'farm_id' => $farmId,
                'name' => $farmName,
                'owner_id' => $ownerId,
            ],
            request(),
            auth('api')->id()
        );

        return response()->json([
            'message' => 'Deleted',
        ]);
    }
}