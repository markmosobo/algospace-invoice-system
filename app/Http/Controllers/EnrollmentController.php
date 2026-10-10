<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Service;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\CourseCertificate;
use App\Models\StudentAssessment;
use App\Models\CourseAssessment;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class EnrollmentController extends Controller
{
    /**
     * Create an active enrollment and attach course sessions.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'service_id' => 'required|exists:services,id',
        ]);

        $result = DB::transaction(function () use ($validated) {
            $enrollment = Enrollment::create([
                'customer_id' => $validated['customer_id'],
                'service_id' => $validated['service_id'],
                'enrolled_at' => now(),
                'status' => 'active',
            ]);

            $service = Service::with('sessions')
                ->findOrFail($validated['service_id']);

            foreach ($service->sessions as $session) {
                $enrollment->sessions()->create([
                    'course_session_id' => $session->id,
                ]);
            }

            return $enrollment;
        });

        app(AuditLogger::class)->record(
            'enrollment.created',
            "Active enrollment created (ID: {$result->id})",
            $result,
            [
                'enrollment_id' => $result->id,
                'customer_id' => $result->customer_id,
                'service_id' => $result->service_id,
                'status' => $result->status,
                'sessions_assigned' => $result->sessions()->count(),
            ],
            $request,
            auth('api')->id()
        );

        return response()->json([
            'message' => 'Student enrolled successfully',
            'data' => $result->load(['customer', 'service']),
        ]);
    }

    /**
     * List enrollments, customers, and active training courses.
     */
    public function index()
    {
        $enrollments = Enrollment::with(['customer', 'service'])
            ->latest()
            ->get();

        $customers = Customer::select('id', 'name', 'phone')
            ->orderBy('name')
            ->get();

        $courses = Service::where('category', 'Training')
            ->where('is_active', 1)
            ->select('id', 'name', 'price', 'unit', 'category')
            ->orderBy('name')
            ->get();

        return response()->json([
            'enrollments' => $enrollments,
            'customers' => $customers,
            'courses' => $courses,
        ]);
    }

    /**
     * Submit a public enrollment request.
     */
    public function requestEnrollment(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'service_id' => 'required|exists:services,id',
        ]);

        $customer = Customer::firstOrCreate(
            ['phone' => $validated['phone']],
            ['name' => $validated['name']]
        );

        $exists = Enrollment::where('customer_id', $customer->id)
            ->where('service_id', $validated['service_id'])
            ->whereIn('status', ['pending', 'active'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Enrollment already exists or pending approval',
            ], 409);
        }

        $enrollment = Enrollment::create([
            'customer_id' => $customer->id,
            'service_id' => $validated['service_id'],
            'status' => 'pending',
        ]);

        app(AuditLogger::class)->record(
            'enrollment.requested',
            "Enrollment request submitted (ID: {$enrollment->id})",
            $enrollment,
            [
                'enrollment_id' => $enrollment->id,
                'customer_id' => $customer->id,
                'service_id' => $enrollment->service_id,
                'status' => $enrollment->status,
            ],
            $request,
            auth('api')->id()
        );

        return response()->json([
            'message' => 'Enrollment request submitted. We will contact you shortly.',
        ]);
    }

    /**
     * Approve an enrollment request.
     */
    public function approve($id)
    {
        $enrollment = Enrollment::findOrFail($id);
        $oldStatus = $enrollment->status;

        $enrollment->update([
            'status' => 'active',
            'enrolled_at' => now(),
        ]);

        app(AuditLogger::class)->record(
            'enrollment.approved',
            "Enrollment approved (ID: {$enrollment->id})",
            $enrollment,
            [
                'enrollment_id' => $enrollment->id,
                'customer_id' => $enrollment->customer_id,
                'service_id' => $enrollment->service_id,
                'previous_status' => $oldStatus,
                'new_status' => $enrollment->status,
            ],
            request(),
            auth('api')->id()
        );

        return response()->json([
            'message' => 'Enrollment approved',
        ]);
    }

    /**
     * Reject and delete an enrollment request.
     */
    public function reject($id)
    {
        $enrollment = Enrollment::findOrFail($id);

        app(AuditLogger::class)->record(
            'enrollment.rejected',
            "Enrollment rejected (ID: {$enrollment->id})",
            $enrollment,
            [
                'enrollment_id' => $enrollment->id,
                'customer_id' => $enrollment->customer_id,
                'service_id' => $enrollment->service_id,
                'previous_status' => $enrollment->status,
            ],
            request(),
            auth('api')->id()
        );

        $enrollment->delete();

        return response()->json([
            'message' => 'Enrollment rejected',
        ]);
    }

    /**
     * Display enrollment details.
     */
    public function show(Enrollment $enrollment)
    {
        $enrollment->load([
            'customer',
            'service',
            'invoice',
            'sessions.session',
            'sessions.session.assessments',
            'sessions.assessments.assessment',
        ]);

        return response()->json([
            'data' => $enrollment,
        ]);
    }

    /**
     * Create or update a student's assessment.
     */
    public function storeAssessment(Request $request, Enrollment $enrollment)
    {
        $validated = $request->validate([
            'course_assessment_id' => 'required|exists:course_assessments,id',
            'score' => 'required|numeric|min:0',
            'remarks' => 'nullable|string',
            'attachment' => 'nullable|file|max:10240',
        ]);

        $assessment = CourseAssessment::findOrFail(
            $validated['course_assessment_id']
        );

        if ($validated['score'] > $assessment->max_marks) {
            return response()->json([
                'message' => 'Score cannot exceed maximum marks.',
            ], 422);
        }

        $existingAssessment = StudentAssessment::where(
            'course_assessment_id',
            $assessment->id
        )
            ->where('enrollment_id', $enrollment->id)
            ->first();

        $oldValues = $existingAssessment
            ? $existingAssessment->only([
                'score',
                'percentage',
                'grade',
                'remarks',
            ])
            : null;

        $attachmentPath = $existingAssessment?->attachment;

        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')
                ->store('student-assessments', 'public');
        }

        $percentage = $assessment->max_marks > 0
            ? round(($validated['score'] / $assessment->max_marks) * 100, 2)
            : 0;

        $studentAssessment = StudentAssessment::updateOrCreate(
            [
                'course_assessment_id' => $assessment->id,
                'enrollment_id' => $enrollment->id,
            ],
            [
                'score' => $validated['score'],
                'percentage' => $percentage,
                'grade' => $this->calculateGrade($percentage),
                'remarks' => $validated['remarks'] ?? null,
                'attachment' => $attachmentPath,
                'assessment_date' => now(),
            ]
        );

        $event = $existingAssessment
            ? 'student_assessment.updated'
            : 'student_assessment.created';

        $properties = [
            'student_assessment_id' => $studentAssessment->id,
            'enrollment_id' => $enrollment->id,
            'course_assessment_id' => $assessment->id,
            'score' => $studentAssessment->score,
            'percentage' => $studentAssessment->percentage,
            'grade' => $studentAssessment->grade,
            'attachment_uploaded' => $request->hasFile('attachment'),
        ];

        if ($oldValues !== null) {
            $properties['previous_values'] = $oldValues;
        }

        app(AuditLogger::class)->record(
            $event,
            "Student assessment " .
                ($existingAssessment ? 'updated' : 'recorded') .
                " (ID: {$studentAssessment->id})",
            $studentAssessment,
            $properties,
            $request,
            auth('api')->id()
        );

        return response()->json([
            'message' => 'Assessment saved successfully.',
            'data' => $studentAssessment->load('assessment'),
        ], 201);
    }

    /**
     * Calculate the assessment grade.
     */
    private function calculateGrade($percentage)
    {
        return match (true) {
            $percentage >= 80 => 'A',
            $percentage >= 70 => 'B',
            $percentage >= 60 => 'C',
            $percentage >= 50 => 'D',
            default => 'E',
        };
    }

    /**
     * Retrieve payments associated with an enrollment invoice.
     */
    public function payments(Enrollment $enrollment)
    {
        $payments = Payment::where(
            'invoice_id',
            $enrollment->invoice_id
        )
            ->orderBy('payment_date', 'desc')
            ->get();

        return response()->json([
            'data' => $payments,
        ]);
    }

    /**
     * Retrieve enrollment progress.
     */
    public function progress(Enrollment $enrollment)
    {
        return response()->json([
            'data' => $enrollment->load('sessions.session'),
        ]);
    }

    /**
     * Update completion status for a course session.
     */
    public function updateProgress(
        Request $request,
        Enrollment $enrollment
    ) {
        $validated = $request->validate([
            'session_id' => 'required',
            'completed' => 'required|boolean',
        ]);

        $studentSession = $enrollment->sessions()
            ->where('course_session_id', $validated['session_id'])
            ->firstOrFail();

        $oldCompleted = (bool) $studentSession->completed;
        $newCompleted = (bool) $validated['completed'];

        $oldProgress = $enrollment->progress_percent;

        $studentSession->update([
            'completed' => $newCompleted,
            'completed_at' => $newCompleted ? now() : null,
        ]);

        $total = $enrollment->sessions()->count();

        $completed = $enrollment->sessions()
            ->where('completed', true)
            ->count();

        $newProgress = $total
            ? round(($completed / $total) * 100)
            : 0;

        $enrollment->update([
            'progress_percent' => $newProgress,
        ]);

        if (
            $oldCompleted !== $newCompleted ||
            $oldProgress != $newProgress
        ) {
            app(AuditLogger::class)->record(
                'enrollment.progress_updated',
                "Enrollment progress updated (ID: {$enrollment->id})",
                $enrollment,
                [
                    'enrollment_id' => $enrollment->id,
                    'session_id' => $validated['session_id'],
                    'session_completed_before' => $oldCompleted,
                    'session_completed_after' => $newCompleted,
                    'progress_before' => $oldProgress,
                    'progress_after' => $newProgress,
                ],
                $request,
                auth('api')->id()
            );
        }

        return response()->json([
            'message' => 'Progress updated',
        ]);
    }

    /**
     * Generate a course certificate PDF.
     */
    public function certificate(Enrollment $enrollment)
    {
        $enrollment->load([
            'customer',
            'service',
            'assessments.assessment',
        ]);

        $certificate = $enrollment->certificate;
        $newlyIssued = false;

        if (!$certificate) {
            $assessments = $enrollment->assessments()
                ->with('assessment')
                ->get();

            $total = $assessments->sum('score');

            $maximum = $assessments->sum(function ($assessment) {
                return $assessment->assessment->max_marks ?? 0;
            });

            $percentage = $maximum > 0
                ? round(($total / $maximum) * 100, 2)
                : 0;

            $grade = match (true) {
                $percentage >= 80 => 'Distinction',
                $percentage >= 70 => 'Credit',
                $percentage >= 50 => 'Pass',
                default => 'Needs Improvement',
            };

            $certificate = CourseCertificate::create([
                'enrollment_id' => $enrollment->id,
                'certificate_no' => 'ALG-' . date('Y') . '-' .
                    str_pad(
                        $enrollment->id,
                        5,
                        '0',
                        STR_PAD_LEFT
                    ),
                'percentage' => $percentage,
                'grade' => $grade,
                'issued_date' => now(),
                'issued_by' => auth()->user()->name ?? 'AlgoSpace',
            ]);

            $newlyIssued = true;

            app(AuditLogger::class)->record(
                'course_certificate.issued',
                "Course certificate issued (ID: {$certificate->id})",
                $certificate,
                [
                    'certificate_id' => $certificate->id,
                    'certificate_no' => $certificate->certificate_no,
                    'enrollment_id' => $enrollment->id,
                    'percentage' => $certificate->percentage,
                    'grade' => $certificate->grade,
                ],
                request(),
                auth('api')->id()
            );
        }

        $certificate->load([
            'enrollment.customer',
            'enrollment.service',
        ]);

        $verificationUrl = url(
            '/verify/' . $certificate->certificate_no
        );

        $qrSvg = QrCode::format('svg')
            ->size(300)
            ->margin(2)
            ->generate($verificationUrl);

        $qrCode = base64_encode($qrSvg);

        $pdf = Pdf::loadView(
            'certificates.course',
            [
                'certificate' => $certificate,
                'enrollment' => $enrollment,
                'qrCode' => $qrCode,
                'verificationUrl' => $verificationUrl,
            ]
        );

        // Log PDF generation separately from the initial certificate issuance.
        app(AuditLogger::class)->record(
            'course_certificate.pdf_generated',
            "Certificate PDF generated ({$certificate->certificate_no})",
            $certificate,
            [
                'certificate_id' => $certificate->id,
                'certificate_no' => $certificate->certificate_no,
                'enrollment_id' => $enrollment->id,
                'newly_issued' => $newlyIssued,
            ],
            request(),
            auth('api')->id()
        );

        return $pdf->stream(
            'certificate-' . $certificate->certificate_no . '.pdf'
        );
    }

    /**
     * Public certificate verification.
     */
    public function verify($certificate_no)
    {
        $certificate = CourseCertificate::whereRaw(
            'UPPER(certificate_no) = ?',
            [strtoupper($certificate_no)]
        )
            ->with([
                'enrollment.customer',
                'enrollment.service',
            ])
            ->first();

        return view(
            'certificates.verify',
            compact('certificate')
        );
    }
}