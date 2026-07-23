<?php

namespace App\Http\Controllers;


use App\Models\Service;
use App\Models\CourseMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;



class CourseMaterialController extends Controller
{


public function index(Service $service)
{

$materials =
CourseMaterial::with([
    'session'
])
->where('service_id',$service->id)
->orderBy('sort_order')
->get();



return response()->json([
    'data'=>$materials
]);


}



public function store(Request $request, Service $service)
{


$data=$request->validate([


'title'=>'required|string',

'description'=>'nullable|string',

'type'=>'required',

'source'=>'required',

'course_session_id'=>'nullable|exists:course_sessions,id',

'url'=>'nullable',

'file'=>'nullable|file',

'sort_order'=>'nullable|integer'


]);



$data['service_id']=$service->id;



if($request->hasFile('file')){


$data['file']
=
$request->file('file')
->store(
'course-materials',
'public'
);


}



$material =
CourseMaterial::create($data);



return response()->json([

'data'=>$material

],201);


}






public function update(Request $request, CourseMaterial $material)
{


$data=$request->validate([


'title'=>'required|string',

'description'=>'nullable|string',

'type'=>'required',

'source'=>'required',

'url'=>'nullable',

'sort_order'=>'nullable|integer'


]);



$material->update($data);



return response()->json([

'data'=>$material

]);


}





public function destroy(CourseMaterial $material)
{


if($material->file){

Storage::disk('public')
->delete($material->file);

}



$material->delete();



return response()->json([

'message'=>'Material deleted'

]);


}




}