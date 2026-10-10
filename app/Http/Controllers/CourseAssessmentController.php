<?php

namespace App\Http\Controllers;

use App\Models\CourseAssessment;
use App\Models\Service;
use App\Models\StudentAssessment;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseAssessmentController extends Controller
{
    /**
     * Display all assessments.
     */
    public function index()
    {
        $assessments = CourseAssessment::with([
            'service',
            'session'
        ])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $assessments
        ]);
    }

    /**
     * Store new assessment.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'course_session_id' => 'nullable|exists:course_sessions,id',
            'title' => 'required|string|max:255',
            'assessment_type' => 'required|string',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'max_marks' => 'required|numeric',
            'pass_mark' => 'nullable|numeric',
            'sort_order' => 'nullable|integer',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        if ($request->hasFile('attachment')) {
            $validated['attachment'] = $request
                ->file('attachment')
                ->store('course_assessments', 'public');
        }

        $assessment = CourseAssessment::create($validated);

        // Audit: assessment created.
        app(AuditLogger::class)->record(
            'course_assessment.created',
            "Course assessment created: {$assessment->title}",
            $assessment,
            [
                'assessment_id' => $assessment->id,
                'service_id' => $assessment->service_id,
                'course_session_id' => $assessment->course_session_id,
                'assessment_type' => $assessment->assessment_type,
                'max_marks' => $assessment->max_marks,
                'pass_mark' => $assessment->pass_mark,
                'has_attachment' => !empty($assessment->attachment),
            ],
            $request
        );

        return response()->json([
            'success' => true,
            'message' => 'Assessment created successfully',
            'data' => $assessment
        ], 201);
    }

    /**
     * Show single assessment.
     */
    public function show(CourseAssessment $assessment)
    {
        $assessment->load([
            'service',
            'session',
            'studentAssessments.enrollment.customer'
        ]);

        return response()->json([
            'success' => true,
            'data' => $assessment
        ]);
    }

    /**
     * Display gradebook for an assessment.
     */
    public function gradebook(CourseAssessment $assessment)
    {
        $assessment->load('service');

        $students = $assessment->service
            ->enrollments()
            ->with('customer')
            ->get()
            ->map(function ($enrollment) use ($assessment) {
                $assessmentRecord = StudentAssessment::where(
                    'course_assessment_id',
                    $assessment->id
                )
                    ->where('enrollment_id', $enrollment->id)
                    ->first();

                return [
                    'id' => $enrollment->id,
                    'customer' => $enrollment->customer,
                    'score' => $assessmentRecord->score ?? null,
                    'percentage' => $assessmentRecord->percentage ?? null,
                    'grade' => $assessmentRecord->grade ?? null,
                    'remarks' => $assessmentRecord->remarks ?? '',
                    'assessment_id' => $assessmentRecord->id ?? null
                ];
            });

        return response()->json([
            'success' => true,
            'assessment' => $assessment,
            'students' => $students
        ]);
    }

    /**
     * Update assessment.
     */
    public function update(
        Request $request,
        CourseAssessment $assessment
    ) {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'assessment_type' => 'sometimes|string',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'max_marks' => 'sometimes|numeric',
            'pass_mark' => 'nullable|numeric',
            'sort_order' => 'nullable|integer',
            'is_active' => 'sometimes|boolean',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $original = $assessment->only([
            'title',
            'assessment_type',
            'description',
            'instructions',
            'max_marks',
            'pass_mark',
            'sort_order',
            'is_active',
            'attachment',
        ]);

        $oldAttachment = $assessment->attachment;
        $newAttachment = null;

        // Upload the replacement before removing the existing file.
        if ($request->hasFile('attachment')) {
            $newAttachment = $request
                ->file('attachment')
                ->store('course_assessments', 'public');

            if (!$newAttachment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to upload assessment attachment.',
                ], 500);
            }

            $validated['attachment'] = $newAttachment;
        }

        try {
            $assessment->update($validated);
            $assessment->refresh();
        } catch (\Throwable $e) {
            if ($newAttachment) {
                Storage::disk('public')->delete($newAttachment);
            }

            throw $e;
        }

        // Delete the old file after the database update succeeds.
        if (
            $newAttachment &&
            $oldAttachment &&
            $oldAttachment !== $newAttachment
        ) {
            Storage::disk('public')->delete($oldAttachment);
        }

        $changes = [];

        foreach ($original as $field => $oldValue) {
            $newValue = $assessment->{$field};

            if ($oldValue != $newValue) {
                if ($field === 'attachment') {
                    $changes[$field] = [
                        'changed' => true,
                    ];
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
                'course_assessment.updated',
                "Course assessment updated: {$assessment->title}",
                $assessment,
                [
                    'changes' => $changes,
                ],
                $request
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Assessment updated successfully',
            'data' => $assessment
        ]);
    }

    /**
     * Delete assessment.
     */
    public function destroy(
        Request $request,
        CourseAssessment $assessment
    ) {
        $attachment = $assessment->attachment;

        // Record the deletion before removing the model.
        app(AuditLogger::class)->record(
            'course_assessment.deleted',
            "Course assessment deleted: {$assessment->title}",
            $assessment,
            [
                'assessment_id' => $assessment->id,
                'title' => $assessment->title,
                'service_id' => $assessment->service_id,
                'course_session_id' => $assessment->course_session_id,
                'assessment_type' => $assessment->assessment_type,
                'max_marks' => $assessment->max_marks,
                'pass_mark' => $assessment->pass_mark,
                'had_attachment' => !empty($attachment),
            ],
            $request
        );

        $assessment->delete();

        if ($attachment) {
            Storage::disk('public')->delete($attachment);
        }

        return response()->json([
            'success' => true,
            'message' => 'Assessment deleted successfully'
        ]);
    }

    /**
     * Upload assessment paper separately.
     */
    public function uploadAttachment(
        Request $request,
        CourseAssessment $assessment
    ) {
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx|max:10240'
        ]);

        $oldAttachment = $assessment->attachment;

        // Store the new file first.
        $path = $request
            ->file('file')
            ->store('course_assessments', 'public');

        if (!$path) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload assessment file.',
            ], 500);
        }

        try {
            $assessment->update([
                'attachment' => $path
            ]);

            $assessment->refresh();
        } catch (\Throwable $e) {
            Storage::disk('public')->delete($path);

            throw $e;
        }

        // Remove the old attachment after the database update.
        if (
            $oldAttachment &&
            $oldAttachment !== $path
        ) {
            Storage::disk('public')->delete($oldAttachment);
        }

        app(AuditLogger::class)->record(
            'course_assessment.attachment_uploaded',
            "Assessment attachment uploaded: {$assessment->title}",
            $assessment,
            [
                'assessment_id' => $assessment->id,
                'had_previous_attachment' => !empty($oldAttachment),
                'has_attachment' => true,
            ],
            $request
        );

        return response()->json([
            'success' => true,
            'message' => 'Assessment file uploaded',
            'path' => $path
        ]);
    }

    /**
     * Get assessments for a course.
     */
    public function byService(Service $service)
    {
        $assessments = $service
            ->assessments()
            ->with('session')
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $assessments
        ]);
    }
}