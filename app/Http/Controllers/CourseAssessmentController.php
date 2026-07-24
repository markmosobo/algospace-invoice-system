<?php

namespace App\Http\Controllers;

use App\Models\CourseAssessment;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseAssessmentController extends Controller
{

    /**
     * Display all assessments
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
            'success'=>true,
            'data'=>$assessments
        ]);
    }



    /**
     * Store new assessment
     */
    public function store(Request $request)
    {


    $validated=$request->validate([


    'service_id'=>'required|exists:services,id',

    'course_session_id'=>'nullable|exists:course_sessions,id',

    'title'=>'required|string|max:255',

    'assessment_type'=>'required|string',

    'description'=>'nullable|string',

    'instructions'=>'nullable|string',

    'max_marks'=>'required|numeric',

    'pass_mark'=>'nullable|numeric',

    'sort_order'=>'nullable|integer',

    'attachment'=>'nullable|file|mimes:pdf,doc,docx|max:10240',


    ]);





    if($request->hasFile('attachment')){


    $validated['attachment'] =
    $request->file('attachment')
    ->store('course_assessments');


    }



    $assessment =
    CourseAssessment::create($validated);



    return response()->json([

    'success'=>true,

    'message'=>'Assessment created successfully',

    'data'=>$assessment

    ],201);


    }




    /**
     * Show single assessment
     */
    public function show(CourseAssessment $assessment)
    {

        $assessment->load([
            'service',
            'session',
            'studentAssessments.enrollment.customer'
        ]);


        return response()->json([

            'success'=>true,

            'data'=>$assessment

        ]);

    }




    /**
     * Update assessment
     */
    public function update(Request $request, CourseAssessment $assessment
    {


        $validated=$request->validate([


        'title'=>'sometimes|string|max:255',

        'assessment_type'=>'sometimes|string',

        'description'=>'nullable|string',

        'instructions'=>'nullable|string',

        'max_marks'=>'sometimes|numeric',

        'pass_mark'=>'nullable|numeric',

        'sort_order'=>'nullable|integer',

        'is_active'=>'boolean',

        'attachment'=>'nullable|file|mimes:pdf,doc,docx|max:10240'


        ]);





        if($request->hasFile('attachment')){


        if($assessment->attachment){

        Storage::delete(
        $assessment->attachment
        );

        }



        $validated['attachment'] =
        $request->file('attachment')
        ->store('course_assessments');


        }




        $assessment->update($validated);



        return response()->json([

        'success'=>true,

        'message'=>'Assessment updated successfully',

        'data'=>$assessment

        ]);


    }





    /**
     * Delete assessment
     */
    public function destroy(CourseAssessment $assessment)
    {

        $assessment->delete();


        return response()->json([

            'success'=>true,

            'message'=>'Assessment deleted successfully'

        ]);

    }





    /**
     * Upload assessment paper
     */
    public function uploadAttachment(
        Request $request,
        CourseAssessment $assessment
    )
    {

        $request->validate([

            'file'=>'required|file|mimes:pdf,doc,docx|max:10240'

        ]);



        if($assessment->attachment){

            Storage::delete($assessment->attachment);

        }



        $path = $request
            ->file('file')
            ->store('course_assessments');



        $assessment->update([

            'attachment'=>$path

        ]);



        return response()->json([

            'success'=>true,

            'message'=>'Assessment file uploaded',

            'path'=>$path

        ]);

    }





    /**
     * Get assessments for a course(Service)
     */
    public function byService(Service $service)
    {

        $assessments = $service
            ->assessments()
            ->with('session')
            ->orderBy('sort_order')
            ->get();



        return response()->json([

            'success'=>true,

            'data'=>$assessments

        ]);

    }

}