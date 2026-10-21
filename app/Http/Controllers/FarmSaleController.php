<?php

namespace App\Http\Controllers;

use App\Models\FarmSale;
use App\Models\FarmVenture;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

class FarmSaleController extends Controller
{
    /**
     * Display a listing of farm sales.
     */
    public function index()
    {
        $farmventures = FarmVenture::with('farm')->get();
        $farmsales = FarmSale::with('venture')->get();

        return response()->json([
            'farmventures' => $farmventures,
            'farmsales' => $farmsales,
        ]);
    }

    /**
     * Store a newly created farm sale.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'venture_id' => 'required|exists:farm_ventures,id',
            'product_name' => 'required|string|max:255',
            'quantity' => 'required|numeric|gt:0',
            'unit' => 'required|string|max:100',
            'price_per_unit' => 'required|numeric|min:0',
            'buyer' => 'nullable|string|max:255',
            'sale_date' => 'required|date',
        ]);

        $validated['total_amount'] =
            $validated['quantity'] * $validated['price_per_unit'];

        $farmsale = FarmSale::create($validated);

        app(AuditLogger::class)->record(
            'farm_sale.created',
            "Farm sale created (ID: {$farmsale->id})",
            $farmsale,
            [
                'farm_sale_id' => $farmsale->id,
                'venture_id' => $farmsale->venture_id,
                'product_name' => $farmsale->product_name,
                'quantity' => $farmsale->quantity,
                'unit' => $farmsale->unit,
                'price_per_unit' => $farmsale->price_per_unit,
                'total_amount' => $farmsale->total_amount,
                'sale_date' => $farmsale->sale_date,
            ],
            $request,
            auth('api')->id()
        );

        return response()->json($farmsale, 201);
    }

    /**
     * Display the specified farm sale.
     */
    public function show(string $id)
    {
        $farmsale = FarmSale::find($id);

        return response()->json($farmsale);
    }

    /**
     * Update the specified farm sale.
     */
    public function update(Request $request, FarmSale $farmsale)
    {
        $validated = $request->validate([
            'venture_id' => 'sometimes|required|exists:farm_ventures,id',
            'product_name' => 'sometimes|required|string|max:255',
            'quantity' => 'sometimes|required|numeric|gt:0',
            'unit' => 'sometimes|required|string|max:100',
            'price_per_unit' => 'sometimes|required|numeric|min:0',
            'buyer' => 'sometimes|nullable|string|max:255',
            'sale_date' => 'sometimes|required|date',
        ]);

        $fields = [
            'venture_id',
            'product_name',
            'quantity',
            'unit',
            'price_per_unit',
            'buyer',
            'sale_date',
            'total_amount',
        ];

        $before = $farmsale->only($fields);

        // Calculate total using updated values and existing values.
        $quantity = $validated['quantity'] ?? $farmsale->quantity;
        $pricePerUnit = $validated['price_per_unit'] ?? $farmsale->price_per_unit;

        $validated['total_amount'] = $quantity * $pricePerUnit;

        $farmsale->update($validated);
        $farmsale->refresh();

        $changes = [];

        foreach ($fields as $field) {
            $oldValue = $before[$field] ?? null;
            $newValue = $farmsale->getAttribute($field);

            if ($oldValue != $newValue) {
                $changes[$field] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        if (!empty($changes)) {
            app(AuditLogger::class)->record(
                'farm_sale.updated',
                "Farm sale updated (ID: {$farmsale->id})",
                $farmsale,
                [
                    'farm_sale_id' => $farmsale->id,
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
     * Remove the specified farm sale.
     */
    public function destroy(string $id)
    {
        $farmsale = FarmSale::find($id);

        if (!$farmsale) {
            return response()->json([
                'message' => 'Farm sale not found',
            ], 404);
        }

        $saleId = $farmsale->id;
        $ventureId = $farmsale->venture_id;
        $productName = $farmsale->product_name;
        $quantity = $farmsale->quantity;
        $totalAmount = $farmsale->total_amount;

        $farmsale->delete();

        app(AuditLogger::class)->record(
            'farm_sale.deleted',
            "Farm sale deleted (ID: {$saleId})",
            $farmsale,
            [
                'farm_sale_id' => $saleId,
                'venture_id' => $ventureId,
                'product_name' => $productName,
                'quantity' => $quantity,
                'total_amount' => $totalAmount,
            ],
            request(),
            auth('api')->id()
        );

        return response()->json([
            'message' => 'Deleted',
        ]);
    }
}