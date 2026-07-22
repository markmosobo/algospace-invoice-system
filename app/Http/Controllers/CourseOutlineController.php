<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\CourseOutline;
use App\Models\CourseOutlineItem;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;


class CourseOutlineController extends Controller
{


public function show(Service $service)
{

    $outline = CourseOutline::with('items')
        ->where('service_id',$service->id)
        ->first();



    return response()->json([

        'success'=>true,

        'data'=>$outline

    ]);

}





public function store(
    Request $request,
    Service $service
)
{


$request->validate([

    'overview'=>'nullable|string',

    'certificate_information'=>'nullable|string',

    'notes'=>'nullable|string',

    'items'=>'nullable|array'

]);




$outline = CourseOutline::create([

    'service_id'=>$service->id,

    'overview'=>$request->overview,

    'certificate_information'=>$request->certificate_information,

    'notes'=>$request->notes

]);





foreach($request->items ?? [] as $item){


    $outline->items()->create([

        'section'=>$item['section'],

        'title'=>$item['title'],

        'description'=>$item['description'] ?? null,

        'sort_order'=>$item['sort_order'] ?? 1

    ]);

}



return response()->json([

    'success'=>true,

    'message'=>'Course outline created',

    'data'=>$outline->load('items')

],201);


}






public function update(
    Request $request,
    CourseOutline $outline
)
{


$request->validate([

    'overview'=>'nullable|string',

    'certificate_information'=>'nullable|string',

    'notes'=>'nullable|string',

    'items'=>'nullable|array'

]);



$outline->update([

    'overview'=>$request->overview,

    'certificate_information'=>$request->certificate_information,

    'notes'=>$request->notes

]);





// replace items

if($request->has('items')){


    $outline->items()->delete();



    foreach($request->items as $item){


        $outline->items()->create([

            'section'=>$item['section'],

            'title'=>$item['title'],

            'description'=>$item['description'] ?? null,

            'sort_order'=>$item['sort_order'] ?? 1

        ]);

    }

}




return response()->json([

    'success'=>true,

    'message'=>'Outline updated',

    'data'=>$outline->load('items')

]);


}






public function destroy(
    CourseOutline $outline
)
{

    $outline->delete();


    return response()->json([

        'success'=>true,

        'message'=>'Deleted'

    ]);

}

public function pdf($service)
{

    $course = Service::with([
        'outline.items',
        'sessions.topics'
    ])
    ->findOrFail($service);


    $pdf = Pdf::loadView(
        'pdf.course-outline',
        compact('course')
    );


    return $pdf->stream(
        $course->name.'-outline.pdf'
    );

}

}