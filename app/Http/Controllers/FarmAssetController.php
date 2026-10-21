<?php

namespace App\Http\Controllers;

use App\Models\FarmAsset;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

class FarmAssetController extends Controller
{
    /**
     * Display a listing of farm assets.
     */
    public function index()
    {
        $farmassets = FarmAsset::get();

        return response()->json($farmassets);
    }

    /**
     * Store a newly created farm asset.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_name' => 'required|string|max:255',
            'asset_type' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'cost' => 'nullable|numeric|min:0',
            'condition' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $farmasset = FarmAsset::create($validated);

        app(AuditLogger::class)->record(
            'farm_asset.created',
            "Farm asset created (ID: {$farmasset->id})",
            $farmasset,
            [
                'asset_id' => $farmasset->id,
                'asset_name' => $farmasset->asset_name,
                'asset_type' => $farmasset->asset_type,
                'cost' => $farmasset->cost,
                'condition' => $farmasset->condition,
            ],
            $request,
            auth('api')->id()
        );

        return response()->json($farmasset);
    }

    /**
     * Display the specified farm asset.
     */
    public function show(string $id)
    {
        $farmasset = FarmAsset::find($id);

        return response()->json($farmasset);
    }

    /**
     * Update the specified farm asset.
     */
    public function update(Request $request, FarmAsset $farmasset)
    {
        $validated = $request->validate([
            'asset_name' => 'required|string|max:255',
            'asset_type' => 'sometimes|nullable|string|max:255',
            'purchase_date' => 'sometimes|nullable|date',
            'cost' => 'sometimes|nullable|numeric|min:0',
            'condition' => 'sometimes|nullable|string|max:255',
            'notes' => 'sometimes|nullable|string',
        ]);

        $fields = [
            'asset_name',
            'asset_type',
            'purchase_date',
            'cost',
            'condition',
            'notes',
        ];

        $before = $farmasset->only($fields);

        $farmasset->update($validated);
        $farmasset->refresh();

        $changes = [];

        foreach ($fields as $field) {
            $oldValue = $before[$field] ?? null;
            $newValue = $farmasset->getAttribute($field);

            if ($oldValue != $newValue) {
                $changes[$field] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        if (!empty($changes)) {
            app(AuditLogger::class)->record(
                'farm_asset.updated',
                "Farm asset updated (ID: {$farmasset->id})",
                $farmasset,
                [
                    'asset_id' => $farmasset->id,
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
     * Remove the specified farm asset.
     */
    public function destroy(string $id)
    {
        $farmasset = FarmAsset::find($id);

        if (!$farmasset) {
            return response()->json([
                'message' => 'Farm asset not found',
            ], 404);
        }

        $assetId = $farmasset->id;
        $assetName = $farmasset->asset_name;
        $assetType = $farmasset->asset_type;
        $cost = $farmasset->cost;

        $farmasset->delete();

        app(AuditLogger::class)->record(
            'farm_asset.deleted',
            "Farm asset deleted (ID: {$assetId})",
            $farmasset,
            [
                'asset_id' => $assetId,
                'asset_name' => $assetName,
                'asset_type' => $assetType,
                'cost' => $cost,
            ],
            request(),
            auth('api')->id()
        );

        return response()->json([
            'message' => 'Deleted',
        ]);
    }
}