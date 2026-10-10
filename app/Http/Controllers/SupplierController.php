<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    protected AuditLogger $auditLogger;

    public function __construct(AuditLogger $auditLogger)
    {
        $this->auditLogger = $auditLogger;
    }

    /**
     * Display all suppliers.
     */
    public function index()
    {
        return response()->json(Supplier::get());
    }

    /**
     * Create a supplier.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:1000',
        ]);

        $supplier = DB::transaction(function () use ($validated, $request) {
            $supplier = Supplier::create([
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'email' => $validated['email'] ?? null,
                'address' => $validated['address'] ?? null,
            ]);

            $this->auditLogger->record(
                'supplier.created',
                'Supplier created',
                $supplier,
                [
                    'supplier_id' => $supplier->id,
                    'name' => $supplier->name,
                ],
                $request,
                auth('api')->id()
            );

            return $supplier;
        });

        return response()->json([
            'message' => 'Supplier created successfully',
            'supplier' => $supplier,
        ], 201);
    }

    /**
     * Display a specific supplier.
     */
    public function show(string $id)
    {
        return response()->json(
            Supplier::findOrFail($id)
        );
    }

    /**
     * Update a supplier.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
            'email' => 'sometimes|nullable|email|max:255',
            'address' => 'sometimes|nullable|string|max:1000',
        ]);

        $supplier = DB::transaction(function () use (
            $validated,
            $request,
            $id
        ) {
            $supplier = Supplier::whereKey($id)
                ->lockForUpdate()
                ->firstOrFail();

            $fields = ['name', 'phone', 'email', 'address'];
            $before = $supplier->only($fields);

            foreach ($fields as $field) {
                if (array_key_exists($field, $validated)) {
                    $supplier->{$field} = $validated[$field];
                }
            }

            $supplier->save();

            $after = $supplier->only($fields);
            $changes = [];

            foreach ($after as $field => $value) {
                if (($before[$field] ?? null) != $value) {
                    // Do not copy contact details into the audit log.
                    $changes[$field] = in_array(
                        $field,
                        ['phone', 'email', 'address'],
                        true
                    )
                        ? 'changed'
                        : [
                            'old' => $before[$field] ?? null,
                            'new' => $value,
                        ];
                }
            }

            if (!empty($changes)) {
                $this->auditLogger->record(
                    'supplier.updated',
                    'Supplier updated',
                    $supplier,
                    [
                        'supplier_id' => $supplier->id,
                        'changes' => $changes,
                    ],
                    $request,
                    auth('api')->id()
                );
            }

            return $supplier;
        });

        return response()->json([
            'message' => 'Supplier updated successfully',
            'supplier' => $supplier,
        ]);
    }

    /**
     * Delete a supplier.
     */
    public function destroy(string $id)
    {
        DB::transaction(function () use ($id) {
            $supplier = Supplier::whereKey($id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->auditLogger->record(
                'supplier.deleted',
                'Supplier deleted',
                $supplier,
                [
                    'supplier_id' => $supplier->id,
                    'name' => $supplier->name,
                ],
                request(),
                auth('api')->id()
            );

            $supplier->delete();
        });

        return response()->json([
            'message' => 'Deleted',
        ]);
    }
}