<?php

namespace App\Http\Controllers;

use App\Models\FarmExpense;
use App\Models\FarmVenture;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

class FarmExpenseController extends Controller
{
    /**
     * Display a listing of farm expenses.
     */
    public function index()
    {
        $farmventures = FarmVenture::with('farm')->get();
        $farmexpenses = FarmExpense::with('venture')->get();

        return response()->json([
            'farmventures' => $farmventures,
            'farmexpenses' => $farmexpenses,
        ]);
    }

    /**
     * Store a newly created farm expense.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'venture_id' => 'required|exists:farm_ventures,id',
            'expense_category' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'paid_by' => 'nullable|string|max:255',
            'receipt_no' => 'nullable|string|max:255',
        ]);

        $farmexpense = FarmExpense::create($validated);

        app(AuditLogger::class)->record(
            'farm_expense.created',
            "Farm expense created (ID: {$farmexpense->id})",
            $farmexpense,
            [
                'farm_expense_id' => $farmexpense->id,
                'venture_id' => $farmexpense->venture_id,
                'expense_category' => $farmexpense->expense_category,
                'amount' => $farmexpense->amount,
                'expense_date' => $farmexpense->expense_date,
            ],
            $request,
            auth('api')->id()
        );

        return response()->json($farmexpense, 201);
    }

    /**
     * Display the specified farm expense.
     */
    public function show(string $id)
    {
        $farmexpense = FarmExpense::find($id);

        return response()->json($farmexpense);
    }

    /**
     * Update the specified farm expense.
     */
    public function update(Request $request, FarmExpense $farmexpense)
    {
        $validated = $request->validate([
            'venture_id' => 'sometimes|required|exists:farm_ventures,id',
            'expense_category' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string|max:1000',
            'amount' => 'sometimes|required|numeric|min:0.01',
            'expense_date' => 'sometimes|required|date',
            'receipt_no' => 'sometimes|nullable|string|max:255',
            'paid_by' => 'sometimes|nullable|string|max:255',
        ]);

        $fields = [
            'venture_id',
            'expense_category',
            'description',
            'amount',
            'expense_date',
            'receipt_no',
            'paid_by',
        ];

        $before = $farmexpense->only($fields);

        $farmexpense->update($validated);
        $farmexpense->refresh();

        $changes = [];

        foreach ($fields as $field) {
            $oldValue = $before[$field] ?? null;
            $newValue = $farmexpense->getAttribute($field);

            if ($oldValue != $newValue) {
                $changes[$field] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        if (!empty($changes)) {
            app(AuditLogger::class)->record(
                'farm_expense.updated',
                "Farm expense updated (ID: {$farmexpense->id})",
                $farmexpense,
                [
                    'farm_expense_id' => $farmexpense->id,
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
     * Remove the specified farm expense.
     */
    public function destroy(string $id)
    {
        $farmexpense = FarmExpense::find($id);

        if (!$farmexpense) {
            return response()->json([
                'message' => 'Farm expense not found',
            ], 404);
        }

        $expenseId = $farmexpense->id;
        $ventureId = $farmexpense->venture_id;
        $category = $farmexpense->expense_category;
        $amount = $farmexpense->amount;

        $farmexpense->delete();

        app(AuditLogger::class)->record(
            'farm_expense.deleted',
            "Farm expense deleted (ID: {$expenseId})",
            $farmexpense,
            [
                'farm_expense_id' => $expenseId,
                'venture_id' => $ventureId,
                'expense_category' => $category,
                'amount' => $amount,
            ],
            request(),
            auth('api')->id()
        );

        return response()->json([
            'message' => 'Deleted',
        ]);
    }
}