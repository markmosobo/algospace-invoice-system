<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Service;
use App\Models\Customer;
use App\Models\Payment;
use Illuminate\Http\Request;

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

        'sessions.session'

    ]);


    return response()->json([

        'data'=>$enrollment

    ]);

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
}
