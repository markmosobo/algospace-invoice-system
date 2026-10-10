<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectMedia;
use App\Services\AuditLogger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProjectProgressController extends Controller
{
    /**
     * Fetch project and its progress history.
     */
    public function index(Project $project)
    {
        $progress = $project->media()
            ->latest()
            ->get();

        return response()->json([
            'project' => $project,
            'progress' => $progress,
        ]);
    }

    /**
     * Fetch project with media and progress history.
     */
    public function progress(Project $project)
    {
        return response()->json([
            'project' => $project->load('media'),
            'progress' => $project->media()
                ->latest()
                ->get(),
        ]);
    }

    /**
     * Store a progress update and associated images.
     */
    public function storeProgress(Request $request, Project $project)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string',
            'stage' => 'nullable|string|max:255',
            'created_at' => 'nullable|date',
            'images' => 'sometimes|array',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $uploadedPaths = [];

        try {
            $project = DB::transaction(function () use (
                $request,
                $validated,
                $project,
                &$uploadedPaths
            ) {
                $project = Project::whereKey($project->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $stage = $validated['stage'] ?? 'ideation';

                $oldStage = $project->current_stage;
                $oldProgress = (float) $project->progress;
                $oldStatus = $project->status;

                $createdAt = !empty($validated['created_at'])
                    ? Carbon::parse($validated['created_at'])
                    : now();

                $uploadedCount = 0;

                if ($request->hasFile('images')) {
                    foreach ($request->file('images') as $file) {
                        $path = $file->store('project_media', 'public');

                        if (!$path) {
                            throw new \RuntimeException(
                                'Failed to store a project progress image.'
                            );
                        }

                        $uploadedPaths[] = $path;

                        ProjectMedia::create([
                            'project_id' => $project->id,
                            'file_path' => $path,
                            'file_name' => $file->getClientOriginalName(),
                            'type' => 'image',
                            'notes' => $validated['notes'] ?? null,
                            'stage' => $stage,
                            'created_at' => $createdAt,
                            'uploaded_by' => auth('api')->id(),
                        ]);

                        $uploadedCount++;
                    }
                }

                $newProgress = $project->calculateProgressFromStage($stage);

                // Preserve the existing rule: progress only moves forward.
                if (!is_null($newProgress)) {
                    $project->progress = max(
                        $project->progress,
                        $newProgress
                    );
                }

                // Preserve completed status as a terminal state.
                if ($project->status !== 'completed') {
                    if ($project->progress >= 100) {
                        $project->status = 'completed';
                    } elseif ($project->progress >= 70) {
                        $project->status = 'active';
                    } else {
                        $project->status = 'draft';
                    }
                }

                $project->current_stage = $stage;
                $project->save();

                app(AuditLogger::class)->record(
                    'project.progress_updated',
                    'Project progress updated',
                    $project,
                    [
                        'project_id' => $project->id,
                        'previous_stage' => $oldStage,
                        'new_stage' => $project->current_stage,
                        'previous_progress' => $oldProgress,
                        'new_progress' => (float) $project->progress,
                        'previous_status' => $oldStatus,
                        'new_status' => $project->status,
                        'images_uploaded' => $uploadedCount,
                        'progress_date' => $createdAt->toDateTimeString(),
                        'notes_provided' => !empty($validated['notes']),
                    ],
                    $request,
                    auth('api')->id()
                );

                return $project;
            });
        } catch (\Throwable $e) {
            // Database rollback does not remove files stored on disk.
            if (!empty($uploadedPaths)) {
                Storage::disk('public')->delete($uploadedPaths);
            }

            throw $e;
        }

        return response()->json([
            'message' => 'Progress updated successfully',
            'data' => $project,
        ]);
    }
}