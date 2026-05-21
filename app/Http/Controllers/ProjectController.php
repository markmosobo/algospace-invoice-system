<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    /**
     * GET /api/projects
     * List projects with filters + media
     */
    public function index(Request $request)
    {
        $projects = Project::query()
            ->with('media') // 👈 load progress images
            ->when($request->type, fn ($q) =>
                $q->where('type', $request->type)
            )
            ->when($request->status, fn ($q) =>
                $q->where('status', $request->status)
            )
            ->when($request->board_type, fn ($q) =>
                $q->where('board_type', $request->board_type)
            )
            ->latest()
            ->get();

        return response()->json([
            'data' => $projects
        ]);
    }

    /**
     * POST /api/projects
     * Create new project
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',

            'type' => ['required', Rule::in([
                'business','personal','asset','training'
            ])],

            'board_type' => ['nullable', Rule::in([
                'admin','public'
            ])],

            'status' => ['required', Rule::in([
                'draft','active','blocked','abandoned','milestone','completed','archived'
            ])],

            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',

            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'due_date'   => 'nullable|date',

            'blocker'  => 'nullable|string|max:255',
            'priority' => 'nullable|integer|min:1|max:5',
        ]);

        // 👇 HANDLE IMAGE UPLOAD
        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')
                ->store('projects/covers', 'public');
        }

        $validated['created_by'] = auth()->id();

        $project = Project::create($validated);

        return response()->json([
            'message' => 'Project created successfully',
            'data' => $project->load('media')
        ], 201);
    }

    /**
     * GET /api/projects/{project}
     * Single project with media
     */
    public function show(Project $project)
    {
        return response()->json([
            'data' => $project->load('media')
        ]);
    }

    /**
     * PUT /api/projects/{project}
     * Update project
     */
    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'type' => 'sometimes|in:business,personal,asset,training',
            'board_type' => 'nullable|in:admin,public',
            'status' => 'sometimes|in:draft,active,blocked,abandoned,milestone,completed,archived',

            'cover_image' => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')
                ->store('projects/covers', 'public');
        }

        $project->update($validated);

        return response()->json([
            'message' => 'Project updated',
            'data' => $project->load('media')
        ]);
    }

    /**
     * PATCH /api/projects/{project}/status
     * Quick status update (VERY useful for Vue UI)
     */
    public function updateStatus(Request $request, Project $project)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in([
                'draft','active','blocked','abandoned','milestone','completed','archived'
            ])],
            'blocker' => 'nullable|string|max:255',
        ]);

        $project->update($validated);

        return response()->json([
            'message' => 'Status updated successfully',
            'data'    => $project->load('media')
        ]);
    }

    /**
     * DELETE /api/projects/{project}
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return response()->json([
            'message' => 'Project deleted successfully'
        ]);
    }

    public function toggleBoardType(Project $project)
    {
        $project->board_type = $project->board_type === 'admin'
            ? 'public'
            : 'admin';

        $project->save();

        return response()->json([
            'message' => 'Board type updated',
            'board_type' => $project->board_type
        ]);
    }    
}