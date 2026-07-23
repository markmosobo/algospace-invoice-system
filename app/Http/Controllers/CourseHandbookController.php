<?php

namespace App\Http\Controllers;


use App\Models\Service;
use Barryvdh\DomPDF\Facade\Pdf;
use ZipArchive;
use Illuminate\Support\Facades\Storage;


class CourseHandbookController extends Controller
{


public function pdf(Service $service)
{


$course = Service::with([


'outline.items',


'sessions.topics',


'materials.session'


])
->findOrFail($service->id);



$pdf = Pdf::loadView(
'pdf.course-handbook',
[
'course'=>$course
]
);



$pdf->setPaper(
'A4',
'portrait'
);



return $pdf->stream(
$course->name.'-handbook.pdf'
);



}


public function package(Service $service)
{

    $course = Service::with([
        'outline.items',
        'sessions.topics',
        'materials'
    ])
    ->findOrFail($service->id);



    // Generate handbook

    $pdf = Pdf::loadView(
        'pdf.course-handbook',
        [
            'course'=>$course
        ]
    );



    $pdfPath = storage_path(
        'app/public/'.$course->name.'-handbook.pdf'
    );


    $pdf->save($pdfPath);




    // ZIP location

    $zipPath = storage_path(
        'app/public/'.$course->name.'-package.zip'
    );



    $zip = new ZipArchive;



    if($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE)){



        // Add handbook

        $zip->addFile(
            $pdfPath,
            'Course Handbook.pdf'
        );




        // Add materials

        foreach($course->materials as $material){


            if(!$material->file){
                continue;
            }



            $file = storage_path(
                'app/public/'.$material->file
            );



            if(file_exists($file)){


                $zip->addFile(
                    $file,
                    'Materials/'.basename($file)
                );


            }


        }




        $zip->close();


    }



    return response()->download(
        $zipPath
    );


}

}