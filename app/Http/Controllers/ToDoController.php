<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;

class ToDoController extends Controller
{
    /**
     * Display a listing of all tasks.
     */
    public function index()
    {
        // Return all tasks including soft-deleted if needed
        $todos = Todo::with('delegatedUser')->orderBy('priority', 'desc')->orderBy('created_at')->get();
        return response()->json($todos);
    }

    public function active()
    {
        $todos = Todo::whereIn('status', ['pending', 'deferred'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($todos);
    }

public function dashboard()
{
    // 1. Fetch ALL todos (for accurate counts)
    $allTodos = Todo::select('id', 'status')->get();

    // 2. Count ALL statuses
    $statusCounts = $allTodos
        ->groupBy('status')
        ->map(fn ($group) => $group->count());

    // 3. Fetch ONLY todos you want to display
    $todos = Todo::whereIn('status', ['pending', 'deferred'])
        ->latest()
        ->get();

    return response()->json([
        'todos' => $todos,
        'statusCounts' => $statusCounts
    ]);
}

    public function markDone(ToDo $todo)
    {
        $todo->update([
            'status' => 'completed'
        ]);

        return response()->json([
            'message' => 'To-do marked as done',
            'todo' => $todo
        ]);
    }    

    public function defer(Todo $todo)
    {
        // Prevent deferring completed tasks
        if ($todo->status === 'completed') {
            return response()->json([
                'message' => 'Completed tasks cannot be deferred'
            ], 422);
        }

        $todo->status = 'deferred';
        $todo->save();

        return response()->json([
            'todo' => $todo
        ]);
    }
    public function resume(Todo $todo)
    {
        if ($todo->status !== 'deferred') {
            return response()->json([
                'message' => 'Only deferred tasks can be resumed'
            ], 422);
        }

        $todo->status = 'pending';
        $todo->save();

        return response()->json([
            'todo' => $todo
        ]);
    }        
    /**
     * Store a newly created task.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:cyber,farm,personal,other',
            'priority' => 'nullable|in:high,medium,low',
        ]);

        $todo = Todo::create($validated);

        return response()->json([
            'message' => 'Task created successfully',
            'task' => $todo
        ], 201);
    }

    /**
     * Display the specified task.
     */
    public function show(string $id)
    {
        $todo = Todo::with('delegatedUser')->findOrFail($id);

        return response()->json($todo);
    }

    /**
     * Update the specified task.
     * Can handle status changes, delegation, deferment, or edits.
     */
    public function update(Request $request, string $id)
    {
        $todo = Todo::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|nullable',
            'category' => 'sometimes|in:cyber,farm,personal,other',
            'priority' => 'sometimes|in:high,medium,low',
            'status' => 'sometimes|in:pending,in_progress,completed,deferred,delegated',
            'delegated_to' => 'sometimes|nullable|exists:users,id',
        ]);

        // Handle delegation
        if (isset($validated['status']) && $validated['status'] === 'delegated' && isset($validated['delegated_to'])) {
            $todo->delegateTo($validated['delegated_to']);
        }

        // Handle completion/counter-check
        if (isset($validated['status']) && $validated['status'] === 'completed') {
            $todo->markChecked();
        }

        // Handle deferment
        if (isset($validated['status']) && $validated['status'] === 'deferred') {
            $todo->deferTask();
        }

        // Update other fields
        $todo->update($validated);

        return response()->json([
            'message' => 'Task updated successfully',
            'task' => $todo
        ]);
    }

    /**
     * Remove the specified task (soft delete).
     */
    public function destroy(string $id)
    {
        $todo = Todo::findOrFail($id);
        $todo->delete();

        return response()->json([
            'message' => 'Task deleted successfully'
        ]);
    }
}