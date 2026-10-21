<?php

namespace App\Http\Controllers;

use App\Models\FarmVenture;
use App\Models\Seedling;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SeedlingController extends Controller
{
    protected AuditLogger $auditLogger;

    public function __construct(AuditLogger $auditLogger)
    {
        $this->auditLogger = $auditLogger;
    }

    /**
     * Display farm ventures and seedlings.
     */
    public function index()
    {
        $farmventures = FarmVenture::with('farm')->get();
        $seedlings = Seedling::with('venture')->get();

        return response()->json([
            'farmventures' => $farmventures,
            'seedlings' => $seedlings,
        ]);
    }

    /**
     * Store a newly created seedling.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'venture_id' => 'required|exists:farm_ventures,id',
            'seedling_type' => 'required|string|max:100',
            'species_name' => 'required|string|max:100',
            'date_planted' => 'required|date',
            'quantity' => 'required|integer|min:1',
            'expected_ready_date' => 'nullable|date|after_or_equal:date_planted',
            'survival_rate' => 'nullable|string|max:100',
        ]);

        $seedling = DB::transaction(function () use ($validated, $request) {
            $seedling = Seedling::create($validated);

            $this->auditLogger->record(
                'seedling.created',
                'Seedling record created',
                $seedling,
                [
                    'seedling_id' => $seedling->id,
                    'venture_id' => $seedling->venture_id,
                    'seedling_type' => $seedling->seedling_type,
                    'species_name' => $seedling->species_name,
                    'quantity' => $seedling->quantity,
                    'date_planted' => $seedling->date_planted,
                    'expected_ready_date' => $seedling->expected_ready_date,
                ],
                $request,
                auth('api')->id()
            );

            return $seedling;
        });

        return response()->json($seedling, 201);
    }

    /**
     * Display a specific seedling.
     */
    public function show(string $id)
    {
        $seedling = Seedling::with('venture')->findOrFail($id);

        return response()->json($seedling);
    }

    /**
     * Update an existing seedling.
     */
    public function update(Request $request, Seedling $seedling)
    {
        $validated = $request->validate([
            'venture_id' => 'required|exists:farm_ventures,id',
            'seedling_type' => 'required|string|max:100',
            'species_name' => 'required|string|max:100',
            'date_planted' => 'required|date',
            'quantity' => 'required|integer|min:1',
            'expected_ready_date' => 'nullable|date|after_or_equal:date_planted',
            'survival_rate' => 'nullable|string|max:100',
        ]);

        DB::transaction(function () use (
            $request,
            $seedling,
            $validated
        ) {
            $seedling = Seedling::whereKey($seedling->id)
                ->lockForUpdate()
                ->firstOrFail();

            $fields = [
                'venture_id',
                'seedling_type',
                'species_name',
                'date_planted',
                'quantity',
                'expected_ready_date',
                'survival_rate',
            ];

            $before = $seedling->only($fields);

            $seedling->fill($validated);
            $seedling->save();

            $after = $seedling->only($fields);
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
                $this->auditLogger->record(
                    'seedling.updated',
                    'Seedling record updated',
                    $seedling,
                    [
                        'seedling_id' => $seedling->id,
                        'changes' => $changes,
                    ],
                    $request,
                    auth('api')->id()
                );
            }
        });

        return response()->json([
            'message' => 'Updated',
        ]);
    }

    /**
     * Delete a seedling.
     */
    public function destroy(string $id)
    {
        DB::transaction(function () use ($id, $request = request()) {
            $seedling = Seedling::whereKey($id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->auditLogger->record(
                'seedling.deleted',
                'Seedling record deleted',
                $seedling,
                [
                    'seedling_id' => $seedling->id,
                    'venture_id' => $seedling->venture_id,
                    'seedling_type' => $seedling->seedling_type,
                    'species_name' => $seedling->species_name,
                    'quantity' => $seedling->quantity,
                ],
                $request,
                auth('api')->id()
            );

            $seedling->delete();
        });

        return response()->json([
            'message' => 'Deleted',
        ]);
    }
}