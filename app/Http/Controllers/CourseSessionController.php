<?php

namespace App\Http\Controllers;

use App\Models\CourseSession;
use App\Models\Service;
use App\Models\Enrollment;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CourseSessionController extends Controller
{
    /**
     * GET /services/{service}/sessions
     */
    public function index(Service $service)
    {
        $sessions = CourseSession::where('service_id', $service->id)
            ->with('topics')
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'data' => $sessions,
        ]);
    }

    /**
     * POST /services/{service}/sessions
     */
    public function store(Request $request, Service $service)
    {
        $data = $request->validate([
            'session_number' => 'required|integer',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'duration_hours' => 'nullable|numeric',
            'sort_order' => 'nullable|integer',
        ]);

        $session = DB::transaction(function () use ($data, $service) {
            $data['service_id'] = $service->id;

            $session = CourseSession::create($data);

            // Add the new session to all existing enrollments.
            $enrollments = Enrollment::where(
                'service_id',
                $service->id
            )->get();

            foreach ($enrollments as $enrollment) {
                $enrollment->sessions()->firstOrCreate([
                    'course_session_id' => $session->id,
                ]);
            }

            return $session;
        });

        app(AuditLogger::class)->record(
            'course_session.created',
            "Course session created: {$session->title}",
            $session,
            [
                'session_id' => $session->id,
                'service_id' => $service->id,
                'session_number' => $session->session_number,
                'title' => $session->title,
                'duration_hours' => $session->duration_hours,
                'sort_order' => $session->sort_order,
            ],
            $request
        );

        return response()->json([
            'data' => $session,
        ], 201);
    }

    /**
     * PUT /course-sessions/{session}
     */
    public function update(Request $request, CourseSession $session)
    {
        $data = $request->validate([
            'session_number' => 'required|integer',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'duration_hours' => 'nullable|numeric',
            'sort_order' => 'nullable|integer',
        ]);

        $before = $session->only([
            'session_number',
            'title',
            'description',
            'duration_hours',
            'sort_order',
        ]);

        $session = DB::transaction(function () use ($data, $session) {
            $session->update($data);

            // Ensure every enrollment has this session.
            $enrollments = Enrollment::where(
                'service_id',
                $session->service_id
            )->get();

            foreach ($enrollments as $enrollment) {
                $enrollment->sessions()->firstOrCreate([
                    'course_session_id' => $session->id,
                ]);
            }

            return $session->fresh();
        });

        $after = $session->only([
            'session_number',
            'title',
            'description',
            'duration_hours',
            'sort_order',
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

        if (!empty($changes)) {
            app(AuditLogger::class)->record(
                'course_session.updated',
                "Course session updated: {$session->title}",
                $session,
                [
                    'session_id' => $session->id,
                    'service_id' => $session->service_id,
                    'changes' => $changes,
                ],
                $request
            );
        }

        return response()->json([
            'data' => $session,
        ]);
    }

    /**
     * DELETE /course-sessions/{session}
     */
    public function destroy(Request $request, CourseSession $session)
    {
        $details = [
            'session_id' => $session->id,
            'service_id' => $session->service_id,
            'session_number' => $session->session_number,
            'title' => $session->title,
            'duration_hours' => $session->duration_hours,
            'sort_order' => $session->sort_order,
        ];

        // Record the action before deleting the session.
        app(AuditLogger::class)->record(
            'course_session.deleted',
            "Course session deleted: {$session->title}",
            $session,
            $details,
            $request
        );

        $session->delete();

        return response()->json([
            'message' => 'Session deleted',
        ]);
    }
}