<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    /**
     * GET /api/projects
     * List projects with filters and media.
     */
    public function index(Request $request)
    {
        $projects = Project::query()
            ->with('media')
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
            'data' => $projects,
        ]);
    }

    /**
     * POST /api/projects
     * Create a project.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',

            'type' => ['required', Rule::in([
                'business', 'personal', 'asset', 'training',
            ])],

            'board_type' => ['nullable', Rule::in([
                'admin', 'public',
            ])],

            'status' => ['required', Rule::in([
                'draft', 'active', 'blocked', 'abandoned',
                'milestone', 'completed', 'archived',
            ])],

            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',

            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'due_date' => 'nullable|date',

            'blocker' => 'nullable|string|max:255',
            'priority' => 'nullable|integer|min:1|max:5',
        ]);

        $coverPath = null;

        try {
            $project = DB::transaction(function () use (
                $request,
                $validated,
                &$coverPath
            ) {
                if ($request->hasFile('cover_image')) {
                    $coverPath = $request->file('cover_image')
                        ->store('projects/covers', 'public');

                    $validated['cover_image'] = $coverPath;
                }

                $validated['created_by'] = auth('api')->id();

                $project = Project::create($validated);

                app(AuditLogger::class)->record(
                    'project.created',
                    'Project created',
                    $project,
                    [
                        'project_id' => $project->id,
                        'title' => $project->title,
                        'type' => $project->type,
                        'board_type' => $project->board_type,
                        'status' => $project->status,
                        'cover_image_uploaded' => !empty($coverPath),
                    ],
                    $request,
                    auth('api')->id()
                );

                return $project;
            });
        } catch (\Throwable $e) {
            if ($coverPath) {
                Storage::disk('public')->delete($coverPath);
            }

            throw $e;
        }

        return response()->json([
            'message' => 'Project created successfully',
            'data' => $project->load('media'),
        ], 201);
    }

    /**
     * GET /api/projects/{project}
     * Retrieve a project with media.
     */
    public function show(Project $project)
    {
        return response()->json([
            'data' => $project->load('media'),
        ]);
    }

    /**
     * PUT /api/projects/{project}
     * Update a project.
     */
    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string',
            'type' => 'sometimes|in:business,personal,asset,training',
            'board_type' => 'sometimes|nullable|in:admin,public',
            'status' => 'sometimes|in:draft,active,blocked,abandoned,milestone,completed,archived',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $newCoverPath = null;
        $oldCoverPath = $project->cover_image;

        try {
            $project = DB::transaction(function () use (
                $request,
                $validated,
                $project,
                &$newCoverPath
            ) {
                $project = Project::whereKey($project->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $before = [
                    'title' => $project->title,
                    'type' => $project->type,
                    'board_type' => $project->board_type,
                    'status' => $project->status,
                    'priority' => $project->priority,
                    'cover_image' => $project->cover_image,
                ];

                if ($request->hasFile('cover_image')) {
                    $newCoverPath = $request->file('cover_image')
                        ->store('projects/covers', 'public');

                    $validated['cover_image'] = $newCoverPath;
                }

                $project->fill($validated);
                $changes = $project->getDirty();

                if (!empty($changes)) {
                    $project->save();

                    app(AuditLogger::class)->record(
                        'project.updated',
                        'Project updated',
                        $project,
                        [
                            'project_id' => $project->id,
                            'before' => $before,
                            'after' => [
                                'title' => $project->title,
                                'type' => $project->type,
                                'board_type' => $project->board_type,
                                'status' => $project->status,
                                'priority' => $project->priority,
                                'cover_image' => $project->cover_image,
                            ],
                            'changed_fields' => array_keys($changes),
                        ],
                        $request,
                        auth('api')->id()
                    );
                }

                return $project;
            });
        } catch (\Throwable $e) {
            if ($newCoverPath) {
                Storage::disk('public')->delete($newCoverPath);
            }

            throw $e;
        }

        // Delete the old image only after the new update has succeeded.
        if (
            $newCoverPath &&
            $oldCoverPath &&
            $oldCoverPath !== $newCoverPath
        ) {
            Storage::disk('public')->delete($oldCoverPath);
        }

        return response()->json([
            'message' => 'Project updated',
            'data' => $project->load('media'),
        ]);
    }

    /**
     * PATCH /api/projects/{project}/status
     * Update project status.
     */
    public function updateStatus(Request $request, Project $project)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in([
                'draft', 'active', 'blocked', 'abandoned',
                'milestone', 'completed', 'archived',
            ])],
            'blocker' => 'sometimes|nullable|string|max:255',
        ]);

        $project = DB::transaction(function () use (
            $validated,
            $project,
            $request
        ) {
            $project = Project::whereKey($project->id)
                ->lockForUpdate()
                ->firstOrFail();

            $oldStatus = $project->status;
            $oldBlocker = $project->blocker;

            $project->fill($validated);
            $changes = $project->getDirty();

            if (!empty($changes)) {
                $project->save();

                app(AuditLogger::class)->record(
                    'project.status_updated',
                    'Project status updated',
                    $project,
                    [
                        'project_id' => $project->id,
                        'old_status' => $oldStatus,
                        'new_status' => $project->status,
                        'old_blocker' => $oldBlocker,
                        'new_blocker' => $project->blocker,
                    ],
                    $request,
                    auth('api')->id()
                );
            }

            return $project;
        });

        return response()->json([
            'message' => 'Status updated successfully',
            'data' => $project->load('media'),
        ]);
    }

    /**
     * DELETE /api/projects/{project}
     * Delete a project.
     */
    public function destroy(Project $project)
    {
        $coverPath = $project->cover_image;

        DB::transaction(function () use ($project) {
            $project = Project::whereKey($project->id)
                ->lockForUpdate()
                ->firstOrFail();

            app(AuditLogger::class)->record(
                'project.deleted',
                'Project deleted',
                $project,
                [
                    'project_id' => $project->id,
                    'title' => $project->title,
                    'type' => $project->type,
                    'status' => $project->status,
                    'board_type' => $project->board_type,
                ],
                request(),
                auth('api')->id()
            );

            $project->delete();
        });

        if ($coverPath) {
            Storage::disk('public')->delete($coverPath);
        }

        return response()->json([
            'message' => 'Project deleted successfully',
        ]);
    }

    /**
     * Toggle project board visibility.
     */
    public function toggleBoardType(Project $project)
    {
        $project = DB::transaction(function () use ($project) {
            $project = Project::whereKey($project->id)
                ->lockForUpdate()
                ->firstOrFail();

            $oldBoardType = $project->board_type;

            $project->board_type = $project->board_type === 'admin'
                ? 'public'
                : 'admin';

            $project->save();

            app(AuditLogger::class)->record(
                'project.board_type_toggled',
                'Project board visibility changed',
                $project,
                [
                    'project_id' => $project->id,
                    'old_board_type' => $oldBoardType,
                    'new_board_type' => $project->board_type,
                ],
                request(),
                auth('api')->id()
            );

            return $project;
        });

        return response()->json([
            'message' => 'Board type updated',
            'board_type' => $project->board_type,
        ]);
    }
}