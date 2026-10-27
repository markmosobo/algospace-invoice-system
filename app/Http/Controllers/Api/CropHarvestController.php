<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Crop;
use App\Models\CropHarvest;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class CropHarvestController extends Controller
{
    /**
     * List harvest records with optional filters.
     *
     * GET /api/crop-harvests
     */
    public function index(Request $request)
    {
        $filters = $request->validate([
            'crop_id' => ['nullable', 'integer', 'exists:crops,id'],
            'season' => ['nullable', 'string', 'max:100'],
            'date_from' => ['nullable', 'date'],
            'date_to' => [
                'nullable',
                'date',
                'after_or_equal:date_from',
            ],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $query = CropHarvest::with('crop');

        if (!empty($filters['crop_id'])) {
            $query->where('crop_id', $filters['crop_id']);
        }

        if (!empty($filters['season'])) {
            $query->where('season', $filters['season']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate(
                'harvest_date',
                '>=',
                $filters['date_from']
            );
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate(
                'harvest_date',
                '<=',
                $filters['date_to']
            );
        }

        $harvests = $query
            ->orderByDesc('harvest_date')
            ->orderByDesc('id')
            ->paginate($filters['per_page'] ?? 15);

        $harvests->getCollection()->transform(
            fn (CropHarvest $harvest) => $this->formatHarvest($harvest)
        );

        return response()->json([
            'success' => true,
            'message' => 'Harvest records retrieved successfully.',
            'data' => $harvests,
        ]);
    }

    /**
     * Create a harvest record.
     *
     * POST /api/crop-harvests
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'crop_id' => ['required', 'integer', 'exists:crops,id'],
            'harvest_date' => ['required', 'date'],
            'season' => ['nullable', 'string', 'max:100'],
            'quantity_kg' => ['required', 'numeric', 'min:0.01'],
            'labour_cost' => ['required', 'numeric', 'min:0'],
            'transport_cost' => ['required', 'numeric', 'min:0'],
            'other_cost' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:5000'],
        ]);

        $validated['other_cost'] = $validated['other_cost'] ?? 0;

        $harvest = CropHarvest::create($validated);
        $harvest->load('crop');

        return response()->json([
            'success' => true,
            'message' => 'Harvest recorded successfully.',
            'data' => $this->formatHarvest($harvest),
        ], 201);
    }

    /**
     * Show one harvest record.
     *
     * GET /api/crop-harvests/{cropHarvest}
     */
    public function show(CropHarvest $cropHarvest)
    {
        $cropHarvest->load('crop');

        return response()->json([
            'success' => true,
            'message' => 'Harvest record retrieved successfully.',
            'data' => $this->formatHarvest($cropHarvest),
        ]);
    }

    /**
     * Update a harvest record.
     *
     * PUT/PATCH /api/crop-harvests/{cropHarvest}
     */
    public function update(
        Request $request,
        CropHarvest $cropHarvest
    ) {
        $validated = $request->validate([
            'crop_id' => ['sometimes', 'required', 'integer', 'exists:crops,id'],
            'harvest_date' => ['sometimes', 'required', 'date'],
            'season' => ['sometimes', 'nullable', 'string', 'max:100'],
            'quantity_kg' => ['sometimes', 'required', 'numeric', 'min:0.01'],
            'labour_cost' => ['sometimes', 'required', 'numeric', 'min:0'],
            'transport_cost' => ['sometimes', 'required', 'numeric', 'min:0'],
            'other_cost' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'description' => ['sometimes', 'nullable', 'string', 'max:5000'],
        ]);

        $cropHarvest->update($validated);
        $cropHarvest->load('crop');

        return response()->json([
            'success' => true,
            'message' => 'Harvest record updated successfully.',
            'data' => $this->formatHarvest($cropHarvest),
        ]);
    }

    /**
     * Delete a harvest record.
     *
     * DELETE /api/crop-harvests/{cropHarvest}
     */
    public function destroy(CropHarvest $cropHarvest)
    {
        $cropHarvest->delete();

        return response()->json([
            'success' => true,
            'message' => 'Harvest record deleted successfully.',
        ]);
    }

    /**
     * Calculate totals for a collection of harvest records.
     *
     * GET /api/crop-harvests/summary
     */
    public function summary(Request $request)
    {
        $filters = $request->validate([
            'crop_id' => ['nullable', 'integer', 'exists:crops,id'],
            'season' => ['nullable', 'string', 'max:100'],
            'date_from' => ['nullable', 'date'],
            'date_to' => [
                'nullable',
                'date',
                'after_or_equal:date_from',
            ],
        ]);

        $query = CropHarvest::query();

        if (!empty($filters['crop_id'])) {
            $query->where('crop_id', $filters['crop_id']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate(
                'harvest_date',
                '>=',
                $filters['date_from']
            );
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate(
                'harvest_date',
                '<=',
                $filters['date_to']
            );
        }

        $summary = $query
            ->selectRaw('COUNT(*) as harvest_count')
            ->selectRaw('COALESCE(SUM(quantity_kg), 0) as total_quantity_kg')
            ->selectRaw('COALESCE(SUM(labour_cost), 0) as total_labour_cost')
            ->selectRaw('COALESCE(SUM(transport_cost), 0) as total_transport_cost')
            ->selectRaw('COALESCE(SUM(other_cost), 0) as total_other_cost')
            ->selectRaw(
                'COALESCE(SUM(labour_cost + transport_cost + other_cost), 0) as total_cost'
            )
            ->first();

        $quantity = (float) $summary->total_quantity_kg;
        $cost = (float) $summary->total_cost;

        return response()->json([
            'success' => true,
            'message' => 'Harvest summary retrieved successfully.',
            'data' => [
                'harvest_count' => (int) $summary->harvest_count,
                'total_quantity_kg' => round($quantity, 2),
                'total_labour_cost' => round(
                    (float) $summary->total_labour_cost, 2
                ),
                'total_transport_cost' => round(
                    (float) $summary->total_transport_cost, 2
                ),
                'total_other_cost' => round(
                    (float) $summary->total_other_cost, 2
                ),
                'total_cost' => round($cost, 2),
                'cost_per_kg' => $quantity > 0
                    ? round($cost / $quantity, 2)
                    : null,
            ],
        ]);
    }

    /**
     * Consistent response representation for one harvest.
     */
    private function formatHarvest(CropHarvest $harvest): array
    {
        return [
            'id' => $harvest->id,
            'crop_id' => $harvest->crop_id,
            'crop' => $harvest->crop,
            'harvest_date' => $harvest->harvest_date?->format('Y-m-d'),
            'season' => $harvest->season,
            'quantity_kg' => (float) $harvest->quantity_kg,
            'labour_cost' => (float) $harvest->labour_cost,
            'transport_cost' => (float) $harvest->transport_cost,
            'other_cost' => (float) $harvest->other_cost,
            'total_cost' => $harvest->total_cost,
            'cost_per_kg' => $harvest->cost_per_kg,
            'description' => $harvest->description,
            'created_at' => $harvest->created_at,
            'updated_at' => $harvest->updated_at,
        ];
    }
}