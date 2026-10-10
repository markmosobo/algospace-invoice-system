<?php

namespace App\Http\Controllers;

use App\Models\StudentAssessment;
use App\Models\Enrollment;
use App\Models\CourseAssessment;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class StudentAssessmentController extends Controller
{
    protected AuditLogger $auditLogger;

    public function __construct(AuditLogger $auditLogger)
    {
        $this->auditLogger = $auditLogger;
    }

    /**
     * Display all student assessments.
     */
    public function index()
    {
        $assessments = StudentAssessment::with([
            'assessment.service',
            'assessment.session',
            'enrollment.customer',
            'enrollment.service',
        ])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $assessments,
        ]);
    }

    /**
     * Store a student assessment result.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_assessment_id' => 'required|exists:course_assessments,id',
            'enrollment_id' => 'required|exists:enrollments,id',
            'score' => 'required|numeric|min:0',
            'homework_completed' => 'nullable|boolean',
            'bonus_completed' => 'nullable|boolean',
            'remarks' => 'nullable|string',
            'assessment_date' => 'nullable|date',
        ]);

        $studentAssessment = DB::transaction(function () use (
            $validated,
            $request
        ) {
            $assessment = CourseAssessment::findOrFail(
                $validated['course_assessment_id']
            );

            $maxMarks = (float) $assessment->max_marks;
            $score = (float) $validated['score'];

            if ($maxMarks <= 0) {
                throw ValidationException::withMessages([
                    'course_assessment_id' =>
                        'The assessment must have maximum marks greater than zero.',
                ]);
            }

            if ($score > $maxMarks) {
                throw ValidationException::withMessages([
                    'score' => "The score cannot exceed {$maxMarks} marks.",
                ]);
            }

            $percentage = ($score / $maxMarks) * 100;

            $validated['percentage'] = round($percentage, 2);
            $validated['grade'] = $this->calculateGrade($percentage);

            $studentAssessment = StudentAssessment::create($validated);

            $studentAssessment->load('enrollment');
            $studentAssessment->enrollment->updateProgress();

            $this->auditLogger->record(
                'student_assessment.created',
                'Student assessment result recorded',
                $studentAssessment,
                [
                    'student_assessment_id' => $studentAssessment->id,
                    'enrollment_id' => $studentAssessment->enrollment_id,
                    'course_assessment_id' => $studentAssessment->course_assessment_id,
                    'score' => $score,
                    'max_marks' => $maxMarks,
                    'percentage' => $studentAssessment->percentage,
                    'grade' => $studentAssessment->grade,
                ],
                $request,
                auth('api')->id()
            );

            return $studentAssessment;
        });

        return response()->json([
            'success' => true,
            'message' => 'Student assessment recorded successfully',
            'data' => $studentAssessment->load([
                'assessment.service',
                'assessment.session',
                'enrollment.customer',
                'enrollment.service',
            ]),
        ], 201);
    }

    /**
     * Show one student assessment.
     */
    public function show(StudentAssessment $studentAssessment)
    {
        $studentAssessment->load([
            'assessment.service',
            'assessment.session',
            'enrollment.customer',
            'enrollment.service',
        ]);

        return response()->json([
            'success' => true,
            'data' => $studentAssessment,
        ]);
    }

    /**
     * Update an assessment result.
     */
    public function update(
        Request $request,
        StudentAssessment $studentAssessment
    ) {
        $validated = $request->validate([
            'score' => 'sometimes|required|numeric|min:0',
            'homework_completed' => 'sometimes|nullable|boolean',
            'bonus_completed' => 'sometimes|nullable|boolean',
            'remarks' => 'sometimes|nullable|string',
            'assessment_date' => 'sometimes|nullable|date',
        ]);

        DB::transaction(function () use (
            $validated,
            $request,
            $studentAssessment
        ) {
            $studentAssessment = StudentAssessment::whereKey(
                $studentAssessment->id
            )->lockForUpdate()->firstOrFail();

            $before = $studentAssessment->only([
                'score',
                'percentage',
                'grade',
                'homework_completed',
                'bonus_completed',
                'remarks',
                'assessment_date',
            ]);

            if (array_key_exists('score', $validated)) {
                $assessment = CourseAssessment::findOrFail(
                    $studentAssessment->course_assessment_id
                );

                $maxMarks = (float) $assessment->max_marks;
                $score = (float) $validated['score'];

                if ($maxMarks <= 0) {
                    throw ValidationException::withMessages([
                        'score' =>
                            'The assessment must have maximum marks greater than zero.',
                    ]);
                }

                if ($score > $maxMarks) {
                    throw ValidationException::withMessages([
                        'score' => "The score cannot exceed {$maxMarks} marks.",
                    ]);
                }

                $percentage = ($score / $maxMarks) * 100;

                $validated['percentage'] = round($percentage, 2);
                $validated['grade'] = $this->calculateGrade($percentage);
            }

            $studentAssessment->update($validated);

            $studentAssessment->load('enrollment');
            $studentAssessment->enrollment->updateProgress();

            $after = $studentAssessment->only(array_keys($before));
            $changes = [];

            foreach ($after as $field => $value) {
                if (($before[$field] ?? null) != $value) {
                    // Avoid storing free-text remarks in the audit log.
                    if ($field === 'remarks') {
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
                $this->auditLogger->record(
                    'student_assessment.updated',
                    'Student assessment result updated',
                    $studentAssessment,
                    [
                        'student_assessment_id' => $studentAssessment->id,
                        'enrollment_id' => $studentAssessment->enrollment_id,
                        'changes' => $changes,
                    ],
                    $request,
                    auth('api')->id()
                );
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Assessment updated successfully',
            'data' => $studentAssessment->fresh([
                'assessment.service',
                'assessment.session',
                'enrollment.customer',
                'enrollment.service',
            ]),
        ]);
    }

    /**
     * Delete an assessment.
     */
    public function destroy(
        Request $request,
        StudentAssessment $studentAssessment
    ) {
        DB::transaction(function () use ($request, $studentAssessment) {
            $studentAssessment = StudentAssessment::whereKey(
                $studentAssessment->id
            )->lockForUpdate()->firstOrFail();

            // Keep the enrollment reference before deleting the assessment.
            $enrollment = Enrollment::findOrFail(
                $studentAssessment->enrollment_id
            );

            $this->auditLogger->record(
                'student_assessment.deleted',
                'Student assessment deleted',
                $studentAssessment,
                [
                    'student_assessment_id' => $studentAssessment->id,
                    'enrollment_id' => $studentAssessment->enrollment_id,
                    'course_assessment_id' => $studentAssessment->course_assessment_id,
                    'score' => $studentAssessment->score,
                    'percentage' => $studentAssessment->percentage,
                    'grade' => $studentAssessment->grade,
                ],
                $request,
                auth('api')->id()
            );

            $studentAssessment->delete();

            $enrollment->updateProgress();
        });

        return response()->json([
            'success' => true,
            'message' => 'Student assessment deleted',
        ]);
    }

    /**
     * Upload a scanned marked assessment.
     */
    public function uploadAttachment(
        Request $request,
        StudentAssessment $studentAssessment
    ) {
        $request->validate([
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $file = $request->file('file');
        $oldPath = $studentAssessment->attachment;

        $path = $file->store('student_assessments');

        if (!$path) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to store the assessment attachment.',
            ], 500);
        }

        try {
            DB::transaction(function () use (
                $request,
                $studentAssessment,
                $path,
                $oldPath
            ) {
                $studentAssessment = StudentAssessment::whereKey(
                    $studentAssessment->id
                )->lockForUpdate()->firstOrFail();

                $previousPath = $studentAssessment->attachment;

                $studentAssessment->update([
                    'attachment' => $path,
                ]);

                $this->auditLogger->record(
                    'student_assessment.attachment_uploaded',
                    'Student assessment attachment uploaded',
                    $studentAssessment,
                    [
                        'student_assessment_id' => $studentAssessment->id,
                        'attachment_replaced' => !empty($previousPath),
                    ],
                    $request,
                    auth('api')->id()
                );

                // Delete the previous file only after the new path is saved.
                if ($previousPath && $previousPath !== $path) {
                    DB::afterCommit(function () use ($previousPath) {
                        Storage::delete($previousPath);
                    });
                }
            });
        } catch (\Throwable $e) {
            Storage::delete($path);
            throw $e;
        }

        return response()->json([
            'success' => true,
            'message' => 'Student assessment uploaded',
            'path' => $path,
        ]);
    }

    /**
     * Get assessments by enrollment.
     */
    public function byEnrollment(Enrollment $enrollment)
    {
        $results = $enrollment
            ->assessments()
            ->with([
                'assessment.session',
            ])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $results,
        ]);
    }

    /**
     * Calculate grade.
     */
    private function calculateGrade($percentage): string
    {
        if ($percentage >= 80) {
            return 'Distinction';
        }

        if ($percentage >= 70) {
            return 'Credit';
        }

        if ($percentage >= 50) {
            return 'Pass';
        }

        return 'Needs Improvement';
    }
}