<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\CourseMaterial;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseMaterialController extends Controller
{
    /**
     * List course materials.
     */
    public function index(Service $service)
    {
        $materials = CourseMaterial::with(['session'])
            ->where('service_id', $service->id)
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'data' => $materials,
        ]);
    }

    /**
     * Create course material.
     */
    public function store(
        Request $request,
        Service $service
    ) {
        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'type' => 'required',
            'source' => 'required',
            'course_session_id' => 'nullable|exists:course_sessions,id',
            'url' => 'nullable',
            'file' => 'nullable|file',
            'sort_order' => 'nullable|integer',
        ]);

        $data['service_id'] = $service->id;

        if ($request->hasFile('file')) {
            $data['file'] = $request->file('file')
                ->store('course-materials', 'public');
        }

        $material = CourseMaterial::create($data);

        app(AuditLogger::class)->record(
            'course_material.created',
            "Course material created: {$material->title}",
            $material,
            [
                'material_id' => $material->id,
                'service_id' => $service->id,
                'course_session_id' => $material->course_session_id,
                'type' => $material->type,
                'source' => $material->source,
                'has_url' => !empty($material->url),
                'has_file' => !empty($material->file),
                'sort_order' => $material->sort_order,
            ],
            $request
        );

        return response()->json([
            'data' => $material,
        ], 201);
    }

    /**
     * Update course material.
     */
    public function update(
        Request $request,
        CourseMaterial $material
    ) {
        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'type' => 'required',
            'source' => 'required',
            'url' => 'nullable',
            'sort_order' => 'nullable|integer',
            'course_session_id' => 'nullable|exists:course_sessions,id',
            'file' => 'nullable|file',
        ]);

        $before = $material->only([
            'title',
            'description',
            'type',
            'source',
            'url',
            'sort_order',
            'course_session_id',
        ]);

        $oldFile = $material->file;
        $newFile = null;

        // Upload replacement before changing the database record.
        if ($request->hasFile('file')) {
            $newFile = $request->file('file')
                ->store('course-materials', 'public');

            $data['file'] = $newFile;
        }

        try {
            $material->update($data);
            $material->refresh();
        } catch (\Throwable $e) {
            // Clean up the newly uploaded file if the update fails.
            if ($newFile) {
                Storage::disk('public')->delete($newFile);
            }

            throw $e;
        }

        // Delete the old file only after the database update succeeds.
        if ($newFile && $oldFile && $oldFile !== $newFile) {
            Storage::disk('public')->delete($oldFile);
        }

        $after = $material->only([
            'title',
            'description',
            'type',
            'source',
            'url',
            'sort_order',
            'course_session_id',
        ]);

        $changes = [];

        foreach ($after as $field => $value) {
            if (($before[$field] ?? null) != $value) {
                $changes[$field] = [
                    'old' => $before[$field] ?? null,
                    'new' => $value,
                ];
            }
        }

        if ($newFile) {
            $changes['file_replaced'] = true;
        }

        if (!empty($changes)) {
            app(AuditLogger::class)->record(
                'course_material.updated',
                "Course material updated: {$material->title}",
                $material,
                [
                    'material_id' => $material->id,
                    'service_id' => $material->service_id,
                    'changes' => $changes,
                ],
                $request
            );
        }

        return response()->json([
            'data' => $material,
        ]);
    }

    /**
     * Delete course material.
     */
    public function destroy(
        Request $request,
        CourseMaterial $material
    ) {
        $file = $material->file;

        // Capture relevant details before deletion.
        $details = [
            'material_id' => $material->id,
            'service_id' => $material->service_id,
            'course_session_id' => $material->course_session_id,
            'title' => $material->title,
            'type' => $material->type,
            'source' => $material->source,
            'had_file' => !empty($file),
        ];

        // Record the deletion before removing the database record.
        app(AuditLogger::class)->record(
            'course_material.deleted',
            "Course material deleted: {$material->title}",
            $material,
            $details,
            $request
        );

        $material->delete();

        if ($file) {
            Storage::disk('public')->delete($file);
        }

        return response()->json([
            'message' => 'Material deleted',
        ]);
    }
}