<?php

namespace App\Http\Controllers;


use App\Models\Enrollment;
use App\Models\Service;
use App\Models\Customer;
use Illuminate\Http\Request;



class CourseEnrollmentController extends Controller
{


public function index(Service $service)
{


$enrollments = Enrollment::with([
    'customer'
])
->where(
    'service_id',
    $service->id
)
->latest()
->get();



return response()->json([

'data'=>$enrollments

]);


}




public function customers()
{


$customers = Customer::orderBy('name')
->get();



return response()->json([

'data'=>$customers

]);


}





public function store(
Request $request,
Service $service
)
{


$data=$request->validate([


'customer_id'=>
'required|exists:customers,id',


'status'=>
'nullable|string',


'is_paid'=>
'boolean',


'amount_paid'=>
'nullable|numeric'


]);



$data['service_id']=$service->id;


$data['enrolled_at']=now();



$enrollment =
Enrollment::create($data);



return response()->json([

'data'=>$enrollment

],201);



}





public function destroy(
Enrollment $enrollment
)
{


$enrollment->delete();



return response()->json([

'message'=>'Enrollment removed'

]);


}



}