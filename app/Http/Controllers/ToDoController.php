<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ToDoController extends Controller
{
    protected AuditLogger $auditLogger;

    public function __construct(AuditLogger $auditLogger)
    {
        $this->auditLogger = $auditLogger;
    }

    /**
     * Display all tasks.
     */
    public function index()
    {
        $todos = Todo::with('delegatedUser')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at')
            ->get();

        return response()->json($todos);
    }

    /**
     * Display active tasks.
     */
    public function active()
    {
        $todos = Todo::whereIn('status', ['pending', 'deferred'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($todos);
    }

    /**
     * Display dashboard tasks and status counts.
     */
    public function dashboard()
    {
        $allTodos = Todo::select('id', 'status')->get();

        $statusCounts = $allTodos
            ->groupBy('status')
            ->map(fn ($group) => $group->count());

        $todos = Todo::latest()->get();

        return response()->json([
            'todos' => $todos,
            'statusCounts' => $statusCounts,
        ]);
    }

    /**
     * Mark a task as completed.
     */
    public function markDone(Request $request, Todo $todo)
    {
        DB::transaction(function () use ($request, $todo) {
            $todo = Todo::whereKey($todo->id)
                ->lockForUpdate()
                ->firstOrFail();

            $oldStatus = $todo->status;

            $todo->update([
                'status' => 'completed',
            ]);

            $this->auditLogger->record(
                'todo.completed',
                'To-do marked as completed',
                $todo,
                [
                    'todo_id' => $todo->id,
                    'title' => $todo->title,
                    'old_status' => $oldStatus,
                    'new_status' => 'completed',
                ],
                $request,
                auth('api')->id()
            );
        });

        return response()->json([
            'message' => 'To-do marked as done',
            'todo' => $todo->fresh(),
        ]);
    }

    /**
     * Defer a task.
     */
    public function defer(Request $request, Todo $todo)
    {
        if ($todo->status === 'completed') {
            return response()->json([
                'message' => 'Completed tasks cannot be deferred',
            ], 422);
        }

        DB::transaction(function () use ($request, $todo) {
            $todo = Todo::whereKey($todo->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($todo->status === 'completed') {
                abort(422, 'Completed tasks cannot be deferred');
            }

            $oldStatus = $todo->status;
            $todo->status = 'deferred';
            $todo->save();

            $this->auditLogger->record(
                'todo.deferred',
                'To-do deferred',
                $todo,
                [
                    'todo_id' => $todo->id,
                    'old_status' => $oldStatus,
                    'new_status' => 'deferred',
                ],
                $request,
                auth('api')->id()
            );
        });

        return response()->json([
            'todo' => $todo->fresh(),
        ]);
    }

    /**
     * Resume a deferred task.
     */
    public function resume(Request $request, Todo $todo)
    {
        if ($todo->status !== 'deferred') {
            return response()->json([
                'message' => 'Only deferred tasks can be resumed',
            ], 422);
        }

        DB::transaction(function () use ($request, $todo) {
            $todo = Todo::whereKey($todo->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($todo->status !== 'deferred') {
                abort(422, 'Only deferred tasks can be resumed');
            }

            $todo->status = 'pending';
            $todo->save();

            $this->auditLogger->record(
                'todo.resumed',
                'Deferred to-do resumed',
                $todo,
                [
                    'todo_id' => $todo->id,
                    'old_status' => 'deferred',
                    'new_status' => 'pending',
                ],
                $request,
                auth('api')->id()
            );
        });

        return response()->json([
            'todo' => $todo->fresh(),
        ]);
    }

    /**
     * Create a task.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:cyber,farm,personal,other',
            'priority' => 'nullable|in:high,medium,low',
        ]);

        $todo = DB::transaction(function () use ($validated, $request) {
            $todo = Todo::create($validated);

            $this->auditLogger->record(
                'todo.created',
                'To-do task created',
                $todo,
                [
                    'todo_id' => $todo->id,
                    'title' => $todo->title,
                    'category' => $todo->category,
                    'priority' => $todo->priority,
                ],
                $request,
                auth('api')->id()
            );

            return $todo;
        });

        return response()->json([
            'message' => 'Task created successfully',
            'task' => $todo,
        ], 201);
    }

    /**
     * Display a task.
     */
    public function show(string $id)
    {
        $todo = Todo::with('delegatedUser')->findOrFail($id);

        return response()->json($todo);
    }

    /**
     * Update a task.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'category' => 'sometimes|required|in:cyber,farm,personal,other',
            'priority' => 'sometimes|nullable|in:high,medium,low',
            'status' => 'sometimes|required|in:pending,in_progress,completed,deferred,delegated',
            'delegated_to' => 'sometimes|nullable|exists:users,id',
        ]);

        $todo = DB::transaction(function () use (
            $validated,
            $request,
            $id
        ) {
            $todo = Todo::whereKey($id)
                ->lockForUpdate()
                ->firstOrFail();

            $before = $todo->only([
                'title',
                'description',
                'category',
                'priority',
                'status',
                'delegated_to',
            ]);

            // Preserve existing model workflow hooks.
            if (
                isset($validated['status']) &&
                $validated['status'] === 'delegated' &&
                isset($validated['delegated_to'])
            ) {
                $todo->delegateTo($validated['delegated_to']);
            }

            if (
                isset($validated['status']) &&
                $validated['status'] === 'completed'
            ) {
                $todo->markChecked();
            }

            if (
                isset($validated['status']) &&
                $validated['status'] === 'deferred'
            ) {
                $todo->deferTask();
            }

            $todo->update($validated);

            $after = $todo->only(array_keys($before));
            $changes = [];

            foreach ($after as $field => $value) {
                if (($before[$field] ?? null) != $value) {
                    if ($field === 'description') {
                        $changes[$field] = 'changed';
                    } else {
                        $changes[$field] = [
                            'old' => $before[$field] ?? null,
                            'new' => $value,
                        ];
                    }
                }
            }

            if (!empty($changes)) {
                $status = $validated['status'] ?? null;

                $event = match ($status) {
                    'completed' => 'todo.completed',
                    'deferred' => 'todo.deferred',
                    'delegated' => 'todo.delegated',
                    default => 'todo.updated',
                };

                $this->auditLogger->record(
                    $event,
                    'To-do task updated',
                    $todo,
                    [
                        'todo_id' => $todo->id,
                        'changes' => $changes,
                    ],
                    $request,
                    auth('api')->id()
                );
            }

            return $todo;
        });

        return response()->json([
            'message' => 'Task updated successfully',
            'task' => $todo,
        ]);
    }

    /**
     * Soft-delete a task.
     */
    public function destroy(string $id)
    {
        DB::transaction(function () use ($id) {
            $todo = Todo::whereKey($id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->auditLogger->record(
                'todo.deleted',
                'To-do task deleted',
                $todo,
                [
                    'todo_id' => $todo->id,
                    'title' => $todo->title,
                    'status' => $todo->status,
                ],
                request(),
                auth('api')->id()
            );

            $todo->delete();
        });

        return response()->json([
            'message' => 'Task deleted successfully',
        ]);
    }
}