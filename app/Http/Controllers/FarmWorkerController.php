<?php

namespace App\Http\Controllers;

use App\Models\FarmWorker;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

class FarmWorkerController extends Controller
{
    /**
     * Display a listing of farm workers.
     */
    public function index()
    {
        $farmworkers = FarmWorker::get();

        return response()->json($farmworkers);
    }

    /**
     * Store a newly created farm worker.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:30',
            'role' => 'nullable|string|max:255',
            'daily_rate' => 'nullable|numeric|min:0',
            'status' => 'nullable|string|max:100',
        ]);

        $farmworker = new FarmWorker();
        $farmworker->name = $validated['name'];
        $farmworker->phone = $validated['phone'] ?? null;
        $farmworker->role = $validated['role'] ?? null;
        $farmworker->daily_rate = $validated['daily_rate'] ?? null;
        $farmworker->status = $validated['status'] ?? null;
        $farmworker->save();

        app(AuditLogger::class)->record(
            'farm_worker.created',
            "Farm worker created (ID: {$farmworker->id})",
            $farmworker,
            [
                'farm_worker_id' => $farmworker->id,
                'name' => $farmworker->name,
                'role' => $farmworker->role,
                'daily_rate' => $farmworker->daily_rate,
                'status' => $farmworker->status,
            ],
            $request,
            auth('api')->id()
        );

        return response()->json($farmworker);
    }

    /**
     * Display the specified farm worker.
     */
    public function show(string $id)
    {
        $farmworker = FarmWorker::find($id);

        return response()->json($farmworker);
    }

    /**
     * Update the specified farm worker.
     */
    public function update(Request $request, FarmWorker $farmworker)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'sometimes|nullable|email|max:255',
            'phone' => 'sometimes|nullable|string|max:30',
            'role' => 'sometimes|nullable|string|max:255',
            'daily_rate' => 'sometimes|nullable|numeric|min:0',
            'status' => 'sometimes|nullable|string|max:100',
        ]);

        $fields = [
            'name',
            'email',
            'phone',
            'role',
            'daily_rate',
            'status',
        ];

        $before = $farmworker->only($fields);

        $farmworker->update($validated);
        $farmworker->refresh();

        $changes = [];

        foreach ($fields as $field) {
            $oldValue = $before[$field] ?? null;
            $newValue = $farmworker->getAttribute($field);

            if ($oldValue != $newValue) {
                // Avoid recording contact information in audit properties.
                if (in_array($field, ['email', 'phone'], true)) {
                    $changes[$field] = ['changed' => true];
                } else {
                    $changes[$field] = [
                        'old' => $oldValue,
                        'new' => $newValue,
                    ];
                }
            }
        }

        if (!empty($changes)) {
            app(AuditLogger::class)->record(
                'farm_worker.updated',
                "Farm worker updated (ID: {$farmworker->id})",
                $farmworker,
                [
                    'farm_worker_id' => $farmworker->id,
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
     * Remove the specified farm worker.
     */
    public function destroy(string $id)
    {
        $farmworker = FarmWorker::find($id);

        if (!$farmworker) {
            return response()->json([
                'message' => 'Farm worker not found',
            ], 404);
        }

        $workerId = $farmworker->id;
        $role = $farmworker->role;
        $status = $farmworker->status;

        $farmworker->delete();

        app(AuditLogger::class)->record(
            'farm_worker.deleted',
            "Farm worker deleted (ID: {$workerId})",
            $farmworker,
            [
                'farm_worker_id' => $workerId,
                'role' => $role,
                'status' => $status,
            ],
            request(),
            auth('api')->id()
        );

        return response()->json([
            'message' => 'Deleted',
        ]);
    }
}