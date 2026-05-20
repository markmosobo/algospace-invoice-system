<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

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
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'note'               => 'nullable|string',
            'progress_increment' => 'nullable|integer|min:1|max:100',
            'images.*'           => 'nullable|image|max:5120'
        ]);

        DB::transaction(function () use ($request, $project, $validated) {

            // 🔁 Update project progress if increment provided
            if (!empty($validated['progress_increment'])) {
                $project->progress = min(
                    100,
                    ($project->progress ?? 0) + $validated['progress_increment']
                );

                // Auto-complete if 100%
                if ($project->progress >= 100) {
                    $project->status = 'completed';
                }

                $project->save();
            }

            // 📸 Store each uploaded image as progress evidence
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {

                    $path = $image->store('project-progress', 'public');

                    ProjectMedia::create([
                        'project_id' => $project->id,
                        'file_path'  => $path,
                        'type'       => 'image',
                        'notes'      => $validated['note'] ?? null,
                        'uploaded_by'=> auth()->id(),
                    ]);
                }
            }

            // 📝 If no image but note exists, still record progress
            if (
                !$request->hasFile('images') &&
                !empty($validated['note'])
            ) {
                ProjectMedia::create([
                    'project_id' => $project->id,
                    'type'       => 'document',
                    'notes'      => $validated['note'],
                    'uploaded_by'=> auth()->id(),
                ]);
            }
        });

        return response()->json([
            'message' => 'Progress added successfully'
        ], 201);
    }
}