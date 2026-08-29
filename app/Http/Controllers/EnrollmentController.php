<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Service;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\CourseCertificate;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\StudentAssessment;
use Illuminate\Support\Facades\Storage;

class EnrollmentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'service_id' => 'required|exists:services,id',
        ]);

        $enrollment = Enrollment::create([

            'customer_id'=>$request->customer_id,

            'service_id'=>$request->service_id,

            'enrolled_at'=>now(),

            'status'=>'active'

        ]);



        $service = Service::with('sessions')
            ->find($request->service_id);



        foreach($service->sessions as $session){


            $enrollment->sessions()->create([

                'course_session_id'=>$session->id

            ]);


        }

        return response()->json([
            'message' => 'Student enrolled successfully',
            'data' => $enrollment->load(['customer', 'service'])
        ]);
    }

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
            'courses' => $courses
        ]);
    }

    public function requestEnrollment(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'phone'      => 'required|string|max:20',
            'service_id' => 'required|exists:services,id',
        ]);

        // 1. Find or create customer by phone
        $customer = Customer::firstOrCreate(
            ['phone' => $request->phone],
            ['name'  => $request->name]
        );

        // 2. Prevent duplicate requests
        $exists = Enrollment::where('customer_id', $customer->id)
            ->where('service_id', $request->service_id)
            ->whereIn('status', ['pending','active'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Enrollment already exists or pending approval'
            ], 409);
        }

        // 3. Create PENDING enrollment
        $enrollment = Enrollment::create([
            'customer_id' => $customer->id,
            'service_id'  => $request->service_id,
            'status'      => 'pending',
        ]);

        return response()->json([
            'message' => 'Enrollment request submitted. We will contact you shortly.'
        ]);
    }
    
    public function approve($id)
    {
        $enrollment = Enrollment::findOrFail($id);

        $enrollment->update([
            'status' => 'active',
            'enrolled_at' => now(),
        ]);

        return response()->json(['message' => 'Enrollment approved']);
    }

    public function reject($id)
    {
        Enrollment::findOrFail($id)->delete();

        return response()->json(['message' => 'Enrollment rejected']);
    }  
    
public function show(Enrollment $enrollment)
{

    $enrollment->load([

        'customer',

        'service',

        'invoice',

        'sessions.session',
        'sessions.session.assessments',
        'sessions.assessments.assessment'

    ]);


    return response()->json([

        'data'=>$enrollment

    ]);

}



public function storeAssessment(Request $request, Enrollment $enrollment)
{
    $validated = $request->validate([
        'course_assessment_id' => 'required|exists:course_assessments,id',
        'score' => 'required|numeric|min:0',
        'remarks' => 'nullable|string',
        'attachment' => 'nullable|file|max:10240',
    ]);

    $assessment = \App\Models\CourseAssessment::findOrFail(
        $validated['course_assessment_id']
    );

    // Make sure score does not exceed maximum marks
    if ($validated['score'] > $assessment->max_marks) {
        return response()->json([
            'message' => 'Score cannot exceed maximum marks.'
        ], 422);
    }

    $attachmentPath = null;

    if ($request->hasFile('attachment')) {
        $attachmentPath = $request
            ->file('attachment')
            ->store('student-assessments', 'public');
    }

    $studentAssessment = StudentAssessment::updateOrCreate(
        [
            'course_assessment_id' => $assessment->id,
            'enrollment_id' => $enrollment->id,
        ],
        [
            'score' => $validated['score'],
            'percentage' => round(
                ($validated['score'] / $assessment->max_marks) * 100,
                2
            ),
            'grade' => $this->calculateGrade(
                ($validated['score'] / $assessment->max_marks) * 100
            ),
            'remarks' => $validated['remarks'] ?? null,
            'attachment' => $attachmentPath,
            'assessment_date' => now(),
        ]
    );

    return response()->json([
        'message' => 'Assessment saved successfully.',
        'data' => $studentAssessment->load('assessment'),
    ], 201);
}

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

public function payments(Enrollment $enrollment)
{


    $payments = Payment::where(

        'invoice_id',

        $enrollment->invoice_id

    )

    ->orderBy(
        'payment_date',
        'desc'
    )

    ->get();



    return response()->json([

        'data'=>$payments

    ]);

}






public function progress(Enrollment $enrollment)
{


    return response()->json([

        'data'=>

        $enrollment->load(

            'sessions.session'

        )

    ]);

}







public function updateProgress(
Request $request,
Enrollment $enrollment
)
{


$request->validate([

    'session_id'=>'required',

    'completed'=>'required|boolean'

]);



$studentSession =

$enrollment

->sessions()

->where(
    'course_session_id',
    $request->session_id
)

->firstOrFail();



$studentSession->update([

    'completed'=>$request->completed,

    'completed_at'=>

    $request->completed

    ? now()

    : null

]);





$total =

$enrollment

->sessions()

->count();



$completed =

$enrollment

->sessions()

->where(
    'completed',
    true
)

->count();




$enrollment->update([

'progress_percent'=>

$total

? round(($completed/$total)*100)

:0

]);



return response()->json([

'message'=>'Progress updated'

]);


}  

public function certificate(Enrollment $enrollment)
{

    // Load required relationships
    $enrollment->load([
        'customer',
        'service',
        'assessments.assessment'
    ]);


    $certificate = $enrollment->certificate;



    if(!$certificate){


        $assessments = $enrollment
            ->assessments()
            ->with('assessment')
            ->get();



        $total = $assessments->sum('score');



        $maximum = $assessments->sum(function($a){

            return $a->assessment->max_marks ?? 0;

        });



        $percentage = $maximum > 0
            ? round(($total / $maximum) * 100, 2)
            : 0;




        $grade = match(true){

            $percentage >= 80 =>
                'Distinction',

            $percentage >= 70 =>
                'Credit',

            $percentage >= 50 =>
                'Pass',

            default =>
                'Needs Improvement'

        };




        $certificate = CourseCertificate::create([

            'enrollment_id' => $enrollment->id,


            'certificate_no' =>
                'ALG-'.date('Y').'-'.
                str_pad(
                    $enrollment->id,
                    5,
                    '0',
                    STR_PAD_LEFT
                ),


            'percentage' => $percentage,


            'grade' => $grade,


            'issued_date' => now(),


            'issued_by' =>
                auth()->user()->name ?? 'AlgoSpace'

        ]);



    }



    // Reload certificate relationship
    $certificate->load([
        'enrollment.customer',
        'enrollment.service'
    ]);



    return PDF::loadView(
        'certificates.course',
        [
            'certificate'=>$certificate,
            'enrollment'=>$enrollment
        ]
    )
    ->stream(
        'certificate-'.$certificate->certificate_no.'.pdf'
    );

}

}
