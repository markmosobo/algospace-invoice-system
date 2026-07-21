<?php

namespace App\Http\Controllers;

use App\Models\StudentAssessment;
use App\Models\Enrollment;
use App\Models\CourseAssessment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudentAssessmentController extends Controller
{


    /**
     * Display all student assessments
     */
    public function index()
    {

        $assessments = StudentAssessment::with([
            'assessment.service',
            'assessment.session',
            'enrollment.customer',
            'enrollment.service'
        ])
        ->latest()
        ->get();



        return response()->json([

            'success'=>true,

            'data'=>$assessments

        ]);

    }





    /**
     * Store student assessment result
     */
    public function store(Request $request)
    {

        $validated = $request->validate([


            'course_assessment_id'=>
                'required|exists:course_assessments,id',


            'enrollment_id'=>
                'required|exists:enrollments,id',


            'score'=>
                'required|numeric',


            'homework_completed'=>
                'nullable|boolean',


            'bonus_completed'=>
                'nullable|boolean',


            'remarks'=>
                'nullable|string',


            'assessment_date'=>
                'nullable|date',


        ]);



        $assessment =
            CourseAssessment::findOrFail(
                $validated['course_assessment_id']
            );



        // Calculate percentage

        $percentage = 0;


        if($assessment->max_marks > 0){

            $percentage =
                ($validated['score']
                /
                $assessment->max_marks)
                * 100;

        }



        $validated['percentage'] =
            round($percentage,2);



        $validated['grade'] =
            $this->calculateGrade(
                $percentage
            );



        $studentAssessment =
            StudentAssessment::create(
                $validated
            );

        // Update course progress
        $studentAssessment
            ->enrollment
            ->updateProgress();


        return response()->json([

            'success'=>true,

            'message'=>'Student assessment recorded successfully',

            'data'=>$studentAssessment

        ],201);


    }






    /**
     * Show one student assessment
     */
    public function show(StudentAssessment $studentAssessment)
    {


        $studentAssessment->load([

            'assessment.service',

            'assessment.session',

            'enrollment.customer',

            'enrollment.service'

        ]);



        return response()->json([

            'success'=>true,

            'data'=>$studentAssessment

        ]);

    }







    /**
     * Update assessment result
     */
    public function update(
        Request $request,
        StudentAssessment $studentAssessment
    )
    {


        $validated = $request->validate([


            'score'=>'sometimes|numeric',

            'homework_completed'=>'nullable|boolean',

            'bonus_completed'=>'nullable|boolean',

            'remarks'=>'nullable|string',

            'assessment_date'=>'nullable|date'


        ]);




        if(isset($validated['score'])){


            $max =
            $studentAssessment
                ->assessment
                ->max_marks;



            $percentage =
            ($validated['score']/$max)*100;



            $validated['percentage']
                = round($percentage,2);



            $validated['grade']
                = $this->calculateGrade(
                    $percentage
                );

        }




        $studentAssessment->update($validated);

        // Refresh progress
        $studentAssessment
            ->enrollment
            ->updateProgress();

        return response()->json([

            'success'=>true,

            'message'=>'Assessment updated successfully',

            'data'=>$studentAssessment

        ]);

    }







    /**
     * Delete assessment
     */
    public function destroy(StudentAssessment $studentAssessment)
    {

        $studentAssessment->delete();

        // Recalculate progress
        $enrollment->updateProgress();


        return response()->json([

            'success'=>true,

            'message'=>'Student assessment deleted'

        ]);

    }







    /**
     * Upload scanned marked assessment
     */
    public function uploadAttachment(
        Request $request,
        StudentAssessment $studentAssessment
    )
    {


        $request->validate([

            'file'=>
            'required|file|mimes:pdf,jpg,jpeg,png|max:10240'

        ]);




        if($studentAssessment->attachment){

            Storage::delete(
                $studentAssessment->attachment
            );

        }




        $path =
        $request
        ->file('file')
        ->store('student_assessments');




        $studentAssessment->update([

            'attachment'=>$path

        ]);




        return response()->json([

            'success'=>true,

            'message'=>'Student assessment uploaded',

            'path'=>$path

        ]);

    }








    /**
     * Get assessments by enrollment
     */
    public function byEnrollment(
        Enrollment $enrollment
    )
    {

        $results =
        $enrollment
        ->assessments()
        ->with([
            'assessment.session'
        ])
        ->get();



        return response()->json([

            'success'=>true,

            'data'=>$results

        ]);

    }








    /**
     * Grade calculator
     */
    private function calculateGrade($percentage)
    {


        if($percentage >= 80){

            return "Distinction";

        }


        if($percentage >=70){

            return "Credit";

        }


        if($percentage >=50){

            return "Pass";

        }


        return "Needs Improvement";


    }


}