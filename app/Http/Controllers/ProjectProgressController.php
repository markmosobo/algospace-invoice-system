<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProjectProgressController extends Controller
{
    /**
     * Fetch project and its progress history
     */
    public function index(Project $project)
    {
        $progress = $project->media()
            ->latest()
            ->get();

        return response()->json([
            'project'  => $project,
            'progress' => $progress
        ]);
    }

    /**
     * Store a new progress update
     */


    public function progress(Project $project)
    {
        return response()->json([
            'project' => $project->load('media'),
            'progress' => $project->media()
                ->latest()
                ->get()
        ]);
    }

    public function storeProgress(Request $request, Project $project)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string',
            'stage' => 'nullable|string',
            'images.*' => 'nullable|image|max:5120'
        ]);

        DB::beginTransaction();

        try {

            $stage = $validated['stage'] ?? 'ideation';

            $project->current_stage = $stage;
            $createdAt = request('created_at')
                ? Carbon::parse(request('created_at'))
                : now();

            // save media
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {

                    $path = $file->store('project_media', 'public');

                    ProjectMedia::create([
                        'project_id' => $project->id,
                        'file_path'  => $path,
                        'file_name'  => $file->getClientOriginalName(),
                        'type'       => 'image',
                        'notes'      => $validated['notes'] ?? null,
                        'stage'      => $stage,
                        'created_at' => $createdAt,
                        'uploaded_by'=> auth()->id(),
                    ]);
                }
            }

            $newProgress = $project->calculateProgressFromStage($stage);

            // only move progress forward
            if (!is_null($newProgress)) {
                $project->progress = max($project->progress, $newProgress);
            }

            // 🔒 TERMINAL STATE GUARD
            if ($project->status !== 'completed') {

                if ($project->progress >= 100) {
                    $project->status = 'completed';
                } elseif ($project->progress >= 70) {
                    $project->status = 'active';
                } else {
                    $project->status = 'draft';
                }
            }

            // stage can still change (history / refinement / notes)
            $project->current_stage = $stage;

            $project->save();

            DB::commit();

            return response()->json([
                'message' => 'Progress updated successfully',
                'data' => $project
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Replace a photo on an existing project progress record.
     */
    public function replacePhoto(Request $request, ProjectMedia $media)
    {
        $validated = $request->validate([
            'image' => 'required|image|max:5120',
        ]);

        $oldPath = $media->file_path;

        // Store the replacement first so the old photo is preserved
        // if the new upload fails.
        $newPath = $request->file('image')->store(
            'project_media',
            'public'
        );

        if (!$newPath) {
            return response()->json([
                'message' => 'Unable to store the replacement photo.',
            ], 500);
        }

        try {
            $media->file_path = $newPath;
            $media->file_name = $request->file('image')
                ->getClientOriginalName();
            $media->type = 'image';
            $media->save();
        } catch (\Throwable $e) {
            // Remove the new file if the database update fails.
            Storage::disk('public')->delete($newPath);

            report($e);

            return response()->json([
                'message' => 'Failed to update the photo record.',
            ], 500);
        }

        // Delete the old file only after the database update succeeds.
        if ($oldPath && $oldPath !== $newPath) {
            Storage::disk('public')->delete($oldPath);
        }

        return response()->json([
            'message' => 'Photo replaced successfully.',
            'data' => $media->fresh(),
        ]);
    }    
}