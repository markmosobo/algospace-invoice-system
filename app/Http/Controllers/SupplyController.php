<?php

namespace App\Http\Controllers;

use App\Models\Restock;
use App\Models\Supplier;
use App\Models\Supply;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SupplyController extends Controller
{
    protected AuditLogger $auditLogger;

    public function __construct(AuditLogger $auditLogger)
    {
        $this->auditLogger = $auditLogger;
    }

    /**
     * Display supplies and suppliers.
     */
    public function index()
    {
        return response()->json([
            'supplies' => Supply::with('supplier')->get(),
            'suppliers' => Supplier::get(),
        ]);
    }

    /**
     * Create a supply item.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'item' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'payment_date' => 'nullable|date',
            'payment_method' => 'nullable|string|max:100',
            'status' => 'nullable|string|max:50',
        ]);

        $supply = DB::transaction(function () use ($validated, $request) {
            $total = $validated['quantity'] * $validated['unit_price'];

            $supply = Supply::create([
                'supplier_id' => $validated['supplier_id'],
                'item' => $validated['item'],
                'quantity' => $validated['quantity'],
                'unit_price' => $validated['unit_price'],
                'total' => $total,
                'payment_date' => $validated['payment_date'] ?? null,
                'method' => $validated['payment_method'] ?? null,
                'status' => $validated['status'] ?? null,
            ]);

            $this->auditLogger->record(
                'supply.created',
                'Supply item created',
                $supply,
                [
                    'supply_id' => $supply->id,
                    'supplier_id' => $supply->supplier_id,
                    'item' => $supply->item,
                    'quantity' => $supply->quantity,
                    'unit_price' => $supply->unit_price,
                    'total' => $supply->total,
                ],
                $request,
                auth('api')->id()
            );

            return $supply;
        });

        return response()->json([
            'message' => 'Supply created successfully',
            'supply' => $supply,
        ], 201);
    }

    /**
     * Display a supply item.
     */
    public function show(string $id)
    {
        return response()->json(
            Supply::with('supplier')->findOrFail($id)
        );
    }

    /**
     * Update a supply item.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'item' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'payment_date' => 'nullable|date',
            'payment_method' => 'nullable|string|max:100',
            'status' => 'nullable|string|max:50',
        ]);

        $supply = DB::transaction(function () use (
            $validated,
            $request,
            $id
        ) {
            $supply = Supply::whereKey($id)
                ->lockForUpdate()
                ->firstOrFail();

            $before = $supply->only([
                'supplier_id',
                'item',
                'quantity',
                'unit_price',
                'total',
                'payment_date',
                'method',
                'status',
            ]);

            $total = $validated['quantity'] * $validated['unit_price'];

            $supply->supplier_id = $validated['supplier_id'];
            $supply->item = $validated['item'];
            $supply->quantity = $validated['quantity'];
            $supply->unit_price = $validated['unit_price'];
            $supply->total = $total;
            $supply->payment_date = $validated['payment_date'] ?? null;
            $supply->method = $validated['payment_method'] ?? null;
            $supply->status = $validated['status'] ?? null;
            $supply->save();

            $after = $supply->only(array_keys($before));
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
                    'supply.updated',
                    'Supply item updated',
                    $supply,
                    [
                        'supply_id' => $supply->id,
                        'changes' => $changes,
                    ],
                    $request,
                    auth('api')->id()
                );
            }

            return $supply;
        });

        return response()->json([
            'message' => 'Supply item updated successfully',
            'supply' => $supply,
        ]);
    }

    /**
     * Delete a supply item.
     */
    public function destroy(string $id)
    {
        DB::transaction(function () use ($id) {
            $supply = Supply::whereKey($id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->auditLogger->record(
                'supply.deleted',
                'Supply item deleted',
                $supply,
                [
                    'supply_id' => $supply->id,
                    'item' => $supply->item,
                    'quantity' => $supply->quantity,
                ],
                request(),
                auth('api')->id()
            );

            $supply->delete();
        });

        return response()->json([
            'message' => 'Deleted',
        ]);
    }

    /**
     * Restock an existing supply item.
     */
    public function restock(Request $request)
    {
        $validated = $request->validate([
            'supply_id' => 'required|integer|exists:supplies,id',
            'quantity' => 'required|integer|min:1',
            'buying_price' => 'required|numeric|min:0',
            'supplier_id' => 'required|integer|exists:suppliers,id',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $product = Supply::whereKey($validated['supply_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $product->quantity += $validated['quantity'];
            $product->save();

            $restock = Restock::create([
                'supply_id' => $product->id,
                'quantity' => $validated['quantity'],
                'buying_price' => $validated['buying_price'],
                'supplier_id' => $validated['supplier_id'],
                'user_id' => auth('api')->id(),
            ]);

            $this->auditLogger->record(
                'supply.restocked',
                'Supply item restocked',
                $product,
                [
                    'supply_id' => $product->id,
                    'restock_id' => $restock->id,
                    'supplier_id' => $validated['supplier_id'],
                    'quantity_added' => $validated['quantity'],
                    'quantity_after' => $product->quantity,
                    'buying_price' => $validated['buying_price'],
                ],
                $request,
                auth('api')->id()
            );
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Product restocked successfully',
        ]);
    }
}