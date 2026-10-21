<?php

namespace App\Http\Controllers;

use App\Models\Seedling;
use App\Models\SeedlingSale;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SeedlingSaleController extends Controller
{
    protected AuditLogger $auditLogger;

    public function __construct(AuditLogger $auditLogger)
    {
        $this->auditLogger = $auditLogger;
    }

    /**
     * Display seedling sales and available seedlings.
     */
    public function index()
    {
        $seedlingsales = SeedlingSale::with('seedling')->get();
        $seedlings = Seedling::with('venture')->get();

        return response()->json([
            'seedlingsales' => $seedlingsales,
            'seedlings' => $seedlings,
        ]);
    }

    /**
     * Store a new seedling sale.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'seedling_id' => 'required|exists:seedlings,id',
            'buyer_name' => 'nullable|string|max:255',
            'quantity_sold' => 'required|numeric|gt:0',
            'price_per_unit' => 'required|numeric|min:0',
            'sale_date' => 'required|date',
        ]);

        $seedlingSale = DB::transaction(function () use ($validated, $request) {
            $seedlingSale = new SeedlingSale();

            $seedlingSale->seedling_id = $validated['seedling_id'];
            $seedlingSale->buyer_name = $validated['buyer_name'] ?? null;
            $seedlingSale->quantity_sold = $validated['quantity_sold'];
            $seedlingSale->price_per_unit = $validated['price_per_unit'];
            $seedlingSale->sale_date = $validated['sale_date'];
            $seedlingSale->total_amount =
                $validated['quantity_sold'] * $validated['price_per_unit'];

            $seedlingSale->save();

            $this->auditLogger->record(
                'seedling_sale.created',
                'Seedling sale recorded',
                $seedlingSale,
                [
                    'seedling_sale_id' => $seedlingSale->id,
                    'seedling_id' => $seedlingSale->seedling_id,
                    'quantity_sold' => $seedlingSale->quantity_sold,
                    'price_per_unit' => $seedlingSale->price_per_unit,
                    'total_amount' => $seedlingSale->total_amount,
                    'sale_date' => $seedlingSale->sale_date,
                ],
                $request,
                auth('api')->id()
            );

            return $seedlingSale;
        });

        return response()->json($seedlingSale, 201);
    }

    /**
     * Display a specific seedling sale.
     */
    public function show(string $id)
    {
        $seedlingSale = SeedlingSale::with('seedling.venture')
            ->findOrFail($id);

        return response()->json($seedlingSale);
    }

    /**
     * Update an existing seedling sale.
     */
    public function update(Request $request, SeedlingSale $seedlingsale)
    {
        $validated = $request->validate([
            'seedling_id' => 'sometimes|required|exists:seedlings,id',
            'buyer_name' => 'sometimes|nullable|string|max:255',
            'quantity_sold' => 'sometimes|required|numeric|gt:0',
            'price_per_unit' => 'sometimes|required|numeric|min:0',
            'sale_date' => 'sometimes|required|date',
        ]);

        $seedlingSale = DB::transaction(function () use (
            $validated,
            $request,
            $seedlingsale
        ) {
            $seedlingSale = SeedlingSale::whereKey($seedlingsale->id)
                ->lockForUpdate()
                ->firstOrFail();

            $fields = [
                'seedling_id',
                'buyer_name',
                'quantity_sold',
                'price_per_unit',
                'sale_date',
                'total_amount',
            ];

            $before = $seedlingSale->only($fields);

            foreach ([
                'seedling_id',
                'buyer_name',
                'quantity_sold',
                'price_per_unit',
                'sale_date',
            ] as $field) {
                if (array_key_exists($field, $validated)) {
                    $seedlingSale->{$field} = $validated[$field];
                }
            }

            // Always recalculate the total from quantity and unit price.
            $seedlingSale->total_amount =
                $seedlingSale->quantity_sold * $seedlingSale->price_per_unit;

            $seedlingSale->save();

            $after = $seedlingSale->only($fields);
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
                    'seedling_sale.updated',
                    'Seedling sale updated',
                    $seedlingSale,
                    [
                        'seedling_sale_id' => $seedlingSale->id,
                        'changes' => $changes,
                    ],
                    $request,
                    auth('api')->id()
                );
            }

            return $seedlingSale;
        });

        return response()->json([
            'message' => 'Updated',
            'seedlingSale' => $seedlingSale,
        ]);
    }

    /**
     * Delete a seedling sale.
     */
    public function destroy(string $id)
    {
        DB::transaction(function () use ($id) {
            $seedlingSale = SeedlingSale::whereKey($id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->auditLogger->record(
                'seedling_sale.deleted',
                'Seedling sale deleted',
                $seedlingSale,
                [
                    'seedling_sale_id' => $seedlingSale->id,
                    'seedling_id' => $seedlingSale->seedling_id,
                    'quantity_sold' => $seedlingSale->quantity_sold,
                    'total_amount' => $seedlingSale->total_amount,
                    'sale_date' => $seedlingSale->sale_date,
                ],
                request(),
                auth('api')->id()
            );

            $seedlingSale->delete();
        });

        return response()->json([
            'message' => 'Deleted',
        ]);
    }
}