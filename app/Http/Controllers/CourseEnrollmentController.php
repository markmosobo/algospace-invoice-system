<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Service;
use App\Models\Customer;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

class CourseEnrollmentController extends Controller
{
    /**
     * List enrollments for a course.
     */
    public function index(Service $service)
    {
        $enrollments = Enrollment::with([
            'customer'
        ])
            ->where('service_id', $service->id)
            ->latest()
            ->get();

        return response()->json([
            'data' => $enrollments
        ]);
    }

    /**
     * List customers available for enrollment.
     */
    public function customers()
    {
        $customers = Customer::orderBy('name')->get();

        return response()->json([
            'data' => $customers
        ]);
    }

    /**
     * Enroll a customer in a course.
     */
    public function store(Request $request, Service $service)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'status' => 'nullable|string',
            'is_paid' => 'sometimes|boolean',
            'amount_paid' => 'nullable|numeric|min:0',
        ]);

        $data['service_id'] = $service->id;
        $data['enrolled_at'] = now();

        $enrollment = Enrollment::create($data);

        // Audit successful enrollment.
        app(AuditLogger::class)->record(
            'course_enrollment.created',
            "Customer enrolled in course: {$service->name}",
            $enrollment,
            [
                'enrollment_id' => $enrollment->id,
                'service_id' => $service->id,
                'service_name' => $service->name,
                'customer_id' => $enrollment->customer_id,
                'status' => $enrollment->status,
                'is_paid' => (bool) $enrollment->is_paid,
                'amount_paid' => $enrollment->amount_paid,
            ],
            $request
        );

        return response()->json([
            'data' => $enrollment
        ], 201);
    }

    /**
     * Remove a course enrollment.
     */
    public function destroy(Request $request, Enrollment $enrollment)
    {
        // Capture details before deleting the enrollment.
        $enrollmentDetails = [
            'enrollment_id' => $enrollment->id,
            'service_id' => $enrollment->service_id,
            'customer_id' => $enrollment->customer_id,
            'status' => $enrollment->status,
            'is_paid' => (bool) $enrollment->is_paid,
            'amount_paid' => $enrollment->amount_paid,
        ];

        $serviceName = Service::whereKey($enrollment->service_id)
            ->value('name') ?? 'Unknown course';

        // Audit before deletion.
        app(AuditLogger::class)->record(
            'course_enrollment.deleted',
            "Customer enrollment removed from course: {$serviceName}",
            $enrollment,
            $enrollmentDetails,
            $request
        );

        $enrollment->delete();

        return response()->json([
            'message' => 'Enrollment removed'
        ]);
    }
}