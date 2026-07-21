<?php

namespace App\Http\Controllers;

use App\Models\CourseSession;
use App\Models\CourseSessionTopic;
use Illuminate\Http\Request;


class CourseSessionTopicController extends Controller
{


public function index(CourseSession $session)
{

return response()->json([
    'data'=>$session->topics
]);

}



public function store(Request $request, CourseSession $session)
{


$data=$request->validate([

'title'=>'required|string',

'description'=>'nullable|string',

'sort_order'=>'nullable|integer'

]);


$data['course_session_id']=$session->id;


$topic=
CourseSessionTopic::create($data);



return response()->json([
'data'=>$topic
],201);


}




public function update(Request $request, CourseSessionTopic $topic)
{


$data=$request->validate([

'title'=>'required|string',

'description'=>'nullable|string',

'sort_order'=>'nullable|integer'

]);


$topic->update($data);


return response()->json([
'data'=>$topic
]);


}




public function destroy(CourseSessionTopic $topic)
{

$topic->delete();


return response()->json([
'message'=>'Topic deleted'
]);

}


}