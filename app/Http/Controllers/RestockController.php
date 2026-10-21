<?php

namespace App\Http\Controllers;

use App\Models\Restock;
use App\Models\Supplier;
use App\Models\Supply;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RestockController extends Controller
{
    protected AuditLogger $auditLogger;

    public function __construct(AuditLogger $auditLogger)
    {
        $this->auditLogger = $auditLogger;
    }

    /**
     * Display restocks, supplies, and suppliers.
     */
    public function index()
    {
        $restocks = Restock::with('supplier', 'supply')->get();
        $supplies = Supply::with('supplier')->get();
        $suppliers = Supplier::get();

        return response()->json([
            'restocks' => $restocks,
            'supplies' => $supplies,
            'suppliers' => $suppliers,
        ]);
    }

    /**
     * Store a newly created restock.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supply_id' => 'required|exists:supplies,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'buying_price' => 'required|numeric|min:0',
            'quantity' => 'required|numeric|min:0.01',
            'status' => 'nullable|string|max:100',
        ]);

        $restock = DB::transaction(function () use ($validated, $request) {
            $restock = Restock::create([
                'supply_id' => $validated['supply_id'],
                'supplier_id' => $validated['supplier_id'],
                'buying_price' => $validated['buying_price'],
                'quantity' => $validated['quantity'],
                'status' => $validated['status'] ?? 'pending',
                'user_id' => auth('api')->id(),
            ]);

            $this->auditLogger->record(
                'restock.created',
                'Restock created',
                $restock,
                [
                    'restock_id' => $restock->id,
                    'supply_id' => $restock->supply_id,
                    'supplier_id' => $restock->supplier_id,
                    'buying_price' => $restock->buying_price,
                    'quantity' => $restock->quantity,
                    'status' => $restock->status,
                ],
                $request,
                auth('api')->id()
            );

            return $restock;
        });

        return response()->json([
            'message' => 'Restock created successfully',
            'restock' => $restock,
        ], 201);
    }

    /**
     * Display a specific restock.
     */
    public function show(string $id)
    {
        $restock = Restock::with('supplier', 'supply')
            ->findOrFail($id);

        return response()->json($restock);
    }

    /**
     * Update an existing restock.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'supply_id' => 'required|exists:supplies,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'buying_price' => 'required|numeric|min:0',
            'quantity' => 'required|numeric|min:0.01',
            'status' => 'sometimes|nullable|string|max:100',
        ]);

        $restock = DB::transaction(function () use (
            $validated,
            $request,
            $id
        ) {
            $restock = Restock::whereKey($id)
                ->lockForUpdate()
                ->firstOrFail();

            $before = $restock->only([
                'supply_id',
                'supplier_id',
                'buying_price',
                'quantity',
                'status',
            ]);

            $restock->supply_id = $validated['supply_id'];
            $restock->supplier_id = $validated['supplier_id'];
            $restock->buying_price = $validated['buying_price'];
            $restock->quantity = $validated['quantity'];
            $restock->status = $validated['status'] ?? $restock->status;
            $restock->user_id = auth('api')->id();
            $restock->save();

            $after = $restock->only([
                'supply_id',
                'supplier_id',
                'buying_price',
                'quantity',
                'status',
            ]);

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
                    'restock.updated',
                    'Restock updated',
                    $restock,
                    [
                        'restock_id' => $restock->id,
                        'changes' => $changes,
                    ],
                    $request,
                    auth('api')->id()
                );
            }

            return $restock;
        });

        return response()->json([
            'message' => 'Restock updated successfully',
            'restock' => $restock,
        ]);
    }

    /**
     * Delete a restock.
     */
    public function destroy(string $id)
    {
        DB::transaction(function () use ($id, $request = request()) {
            $restock = Restock::whereKey($id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->auditLogger->record(
                'restock.deleted',
                'Restock deleted',
                $restock,
                [
                    'restock_id' => $restock->id,
                    'supply_id' => $restock->supply_id,
                    'supplier_id' => $restock->supplier_id,
                    'buying_price' => $restock->buying_price,
                    'quantity' => $restock->quantity,
                    'status' => $restock->status,
                ],
                $request,
                auth('api')->id()
            );

            $restock->delete();
        });

        return response()->json([
            'message' => 'Deleted',
        ]);
    }
}