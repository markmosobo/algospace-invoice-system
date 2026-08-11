<?php

namespace App\Http\Controllers;

use App\Models\CourseSession;
use App\Models\Service;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class CourseSessionController extends Controller
{


    // GET /services/{service}/sessions
    public function index(Service $service)
    {

        $sessions = CourseSession::where('service_id',$service->id)
            ->with('topics')
            ->orderBy('sort_order')
            ->get();


        return response()->json([
            'data'=>$sessions
        ]);

    }




    // POST /services/{service}/sessions
    public function store(Request $request, Service $service)
    {
        $data = $request->validate([
            'session_number' => 'required|integer',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'duration_hours' => 'nullable|numeric',
            'sort_order' => 'nullable|integer'
        ]);

        $data['service_id'] = $service->id;

        // Create the course session
        $session = CourseSession::create($data);

        // Add this session to all existing enrollments
        $enrollments = Enrollment::where(
            'service_id',
            $service->id
        )->get();

        foreach ($enrollments as $enrollment) {

            $enrollment->sessions()->firstOrCreate([
                'course_session_id' => $session->id
            ]);

        }

        return response()->json([
            'data' => $session
        ], 201);
    }





    // PUT /course-sessions/{session}
    public function update(Request $request, CourseSession $session)
    {
        $data = $request->validate([

            'session_number' => 'required|integer',

            'title' => 'required|string',

            'description' => 'nullable|string',

            'duration_hours' => 'nullable|numeric',

            'sort_order' => 'nullable|integer'

        ]);

        // Update the course session
        $session->update($data);

        // Find all students enrolled in this course
        $enrollments = Enrollment::where(
            'service_id',
            $session->service_id
        )->get();

        // Make sure every enrollment has this session
        foreach ($enrollments as $enrollment) {

            $enrollment->sessions()->firstOrCreate([
                'course_session_id' => $session->id
            ]);

        }

        return response()->json([
            'data' => $session
        ]);
    }





    // DELETE /course-sessions/{session}
    public function destroy(CourseSession $session)
    {

        $session->delete();


        return response()->json([
            'message'=>'Session deleted'
        ]);

    }



}