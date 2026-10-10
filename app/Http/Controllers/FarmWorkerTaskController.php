<?php

namespace App\Http\Controllers;

use App\Models\FarmVenture;
use App\Models\FarmWorkerTask;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

class FarmWorkerTaskController extends Controller
{
    /**
     * Display a listing of farm worker tasks.
     */
    public function index()
    {
        $farmventures = FarmVenture::with('farm')->get();
        $farmworkertasks = FarmWorkerTask::get();

        return response()->json([
            'farmventures' => $farmventures,
            'farmworkertasks' => $farmworkertasks,
        ]);
    }

    /**
     * Store a newly created farm worker task.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'worker_id' => 'required|exists:farm_workers,id',
            'venture_id' => 'required|exists:farm_ventures,id',
            'task' => 'required|string|max:1000',
            'work_date' => 'required|date',
            'amount_paid' => 'required|numeric|min:0',
        ]);

        $farmworkertask = FarmWorkerTask::create($validated);

        app(AuditLogger::class)->record(
            'farm_worker_task.created',
            "Farm worker task created (ID: {$farmworkertask->id})",
            $farmworkertask,
            [
                'farm_worker_task_id' => $farmworkertask->id,
                'worker_id' => $farmworkertask->worker_id,
                'venture_id' => $farmworkertask->venture_id,
                'work_date' => $farmworkertask->work_date,
                'amount_paid' => $farmworkertask->amount_paid,
            ],
            $request,
            auth('api')->id()
        );

        return response()->json($farmworkertask);
    }

    /**
     * Display the specified farm worker task.
     */
    public function show(string $id)
    {
        $farmworkertask = FarmWorkerTask::find($id);

        if (!$farmworkertask) {
            return response()->json([
                'message' => 'Farm worker task not found',
            ], 404);
        }

        return response()->json($farmworkertask);
    }

    /**
     * Update the specified farm worker task.
     */
    public function update(Request $request, FarmWorkerTask $farmworkertask)
    {
        $validated = $request->validate([
            'worker_id' => 'sometimes|required|exists:farm_workers,id',
            'venture_id' => 'sometimes|required|exists:farm_ventures,id',
            'task' => 'sometimes|required|string|max:1000',
            'work_date' => 'sometimes|required|date',
            'amount_paid' => 'sometimes|required|numeric|min:0',
        ]);

        $fields = [
            'worker_id',
            'venture_id',
            'task',
            'work_date',
            'amount_paid',
        ];

        $before = $farmworkertask->only($fields);

        $farmworkertask->update($validated);
        $farmworkertask->refresh();

        $changes = [];

        foreach ($fields as $field) {
            $oldValue = $before[$field] ?? null;
            $newValue = $farmworkertask->getAttribute($field);

            if ($oldValue != $newValue) {
                $changes[$field] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        if (!empty($changes)) {
            app(AuditLogger::class)->record(
                'farm_worker_task.updated',
                "Farm worker task updated (ID: {$farmworkertask->id})",
                $farmworkertask,
                [
                    'farm_worker_task_id' => $farmworkertask->id,
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
     * Remove the specified farm worker task.
     */
    public function destroy(string $id)
    {
        $farmworkertask = FarmWorkerTask::find($id);

        if (!$farmworkertask) {
            return response()->json([
                'message' => 'Farm worker task not found',
            ], 404);
        }

        $taskId = $farmworkertask->id;
        $workerId = $farmworkertask->worker_id;
        $ventureId = $farmworkertask->venture_id;
        $amountPaid = $farmworkertask->amount_paid;

        $farmworkertask->delete();

        app(AuditLogger::class)->record(
            'farm_worker_task.deleted',
            "Farm worker task deleted (ID: {$taskId})",
            $farmworkertask,
            [
                'farm_worker_task_id' => $taskId,
                'worker_id' => $workerId,
                'venture_id' => $ventureId,
                'amount_paid' => $amountPaid,
            ],
            request(),
            auth('api')->id()
        );

        return response()->json([
            'message' => 'Deleted',
        ]);
    }
}