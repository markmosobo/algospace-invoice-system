<?php

namespace App\Http\Controllers;

use App\Models\FarmInput;
use App\Models\FarmVenture;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

class FarmInputController extends Controller
{
    /**
     * Display a listing of farm inputs.
     */
    public function index()
    {
        $farmventures = FarmVenture::with('farm')->get();
        $farminputs = FarmInput::with('venture')->get();

        return response()->json([
            'farmventures' => $farmventures,
            'farminputs' => $farminputs,
        ]);
    }

    /**
     * Store a newly created farm input.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'venture_id' => 'required|exists:farm_ventures,id',
            'input_name' => 'required|string|max:255',
            'input_type' => 'nullable|string|max:255',
            'quantity' => 'required|numeric|min:0.01',
            'expense_date' => 'nullable|date',
            'unit' => 'nullable|string|max:100',
            'date_applied' => 'nullable|date',
        ]);

        $farminput = FarmInput::create($validated);

        app(AuditLogger::class)->record(
            'farm_input.created',
            "Farm input created (ID: {$farminput->id})",
            $farminput,
            [
                'farm_input_id' => $farminput->id,
                'venture_id' => $farminput->venture_id,
                'input_name' => $farminput->input_name,
                'input_type' => $farminput->input_type,
                'quantity' => $farminput->quantity,
                'unit' => $farminput->unit,
                'date_applied' => $farminput->date_applied,
            ],
            $request,
            auth('api')->id()
        );

        return response()->json($farminput, 201);
    }

    /**
     * Display the specified farm input.
     */
    public function show(string $id)
    {
        $farminput = FarmInput::find($id);

        return response()->json($farminput);
    }

    /**
     * Update the specified farm input.
     */
    public function update(Request $request, FarmInput $farminput)
    {
        $validated = $request->validate([
            'venture_id' => 'sometimes|required|exists:farm_ventures,id',
            'input_name' => 'sometimes|required|string|max:255',
            'input_type' => 'sometimes|nullable|string|max:255',
            'quantity' => 'sometimes|required|numeric|min:0.01',
            'unit' => 'sometimes|nullable|string|max:100',
            'date_applied' => 'sometimes|nullable|date',
        ]);

        $fields = [
            'venture_id',
            'input_name',
            'input_type',
            'quantity',
            'unit',
            'date_applied',
        ];

        $before = $farminput->only($fields);

        $farminput->update($validated);
        $farminput->refresh();

        $changes = [];

        foreach ($fields as $field) {
            $oldValue = $before[$field] ?? null;
            $newValue = $farminput->getAttribute($field);

            if ($oldValue != $newValue) {
                $changes[$field] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        if (!empty($changes)) {
            app(AuditLogger::class)->record(
                'farm_input.updated',
                "Farm input updated (ID: {$farminput->id})",
                $farminput,
                [
                    'farm_input_id' => $farminput->id,
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
     * Remove the specified farm input.
     */
    public function destroy(string $id)
    {
        $farminput = FarmInput::find($id);

        if (!$farminput) {
            return response()->json([
                'message' => 'Farm input not found',
            ], 404);
        }

        $inputId = $farminput->id;
        $ventureId = $farminput->venture_id;
        $inputName = $farminput->input_name;
        $quantity = $farminput->quantity;
        $unit = $farminput->unit;

        $farminput->delete();

        app(AuditLogger::class)->record(
            'farm_input.deleted',
            "Farm input deleted (ID: {$inputId})",
            $farminput,
            [
                'farm_input_id' => $inputId,
                'venture_id' => $ventureId,
                'input_name' => $inputName,
                'quantity' => $quantity,
                'unit' => $unit,
            ],
            request(),
            auth('api')->id()
        );

        return response()->json([
            'message' => 'Deleted',
        ]);
    }
}