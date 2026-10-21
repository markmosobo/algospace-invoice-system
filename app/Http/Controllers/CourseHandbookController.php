<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Services\AuditLogger;
use Barryvdh\DomPDF\Facade\Pdf;
use ZipArchive;
use Illuminate\Http\Request;

class CourseHandbookController extends Controller
{
    /**
     * Stream a course handbook PDF.
     */
    public function pdf(Request $request, Service $service)
    {
        $course = Service::with([
            'outline.items',
            'sessions.topics',
            'materials.session',
        ])->findOrFail($service->id);

        $pdf = Pdf::loadView(
            'pdf.course-handbook',
            [
                'course' => $course,
            ]
        );

        $pdf->setPaper('A4', 'portrait');

        // Record successful handbook generation.
        app(AuditLogger::class)->record(
            'course_handbook.generated',
            "Course handbook generated for: {$course->name}",
            $course,
            [
                'service_id' => $course->id,
                'course_name' => $course->name,
                'format' => 'pdf',
                'delivery' => 'stream',
            ],
            $request
        );

        return $pdf->stream(
            $course->name . '-handbook.pdf'
        );
    }

    /**
     * Generate and download a ZIP package containing the handbook
     * and available course materials.
     */
    public function package(Request $request, Service $service)
    {
        $course = Service::with([
            'outline.items',
            'sessions.topics',
            'materials',
        ])->findOrFail($service->id);

        // Generate handbook PDF.
        $pdf = Pdf::loadView(
            'pdf.course-handbook',
            [
                'course' => $course,
            ]
        );

        $pdfPath = storage_path(
            'app/public/' . $course->name . '-handbook.pdf'
        );

        // Ensure the destination directory exists.
        if (!is_dir(dirname($pdfPath))) {
            mkdir(dirname($pdfPath), 0755, true);
        }

        $pdf->save($pdfPath);

        // ZIP location.
        $zipPath = storage_path(
            'app/public/' . $course->name . '-package.zip'
        );

        $zip = new ZipArchive();

        $zipResult = $zip->open(
            $zipPath,
            ZipArchive::CREATE | ZipArchive::OVERWRITE
        );

        if ($zipResult !== true) {
            @unlink($pdfPath);

            return response()->json([
                'message' => 'Unable to create the course package.',
            ], 500);
        }

        try {
            // Add handbook.
            $zip->addFile(
                $pdfPath,
                'Course Handbook.pdf'
            );

            // Add available course materials.
            foreach ($course->materials as $material) {
                if (!$material->file) {
                    continue;
                }

                $file = storage_path(
                    'app/public/' . $material->file
                );

                if (file_exists($file)) {
                    $zip->addFile(
                        $file,
                        'Materials/' . basename($file)
                    );
                }
            }

            if (!$zip->close()) {
                @unlink($pdfPath);
                @unlink($zipPath);

                return response()->json([
                    'message' => 'Unable to finalize the course package.',
                ], 500);
            }
        } catch (\Throwable $e) {
            $zip->close();

            @unlink($pdfPath);
            @unlink($zipPath);

            throw $e;
        }

        // Audit only after the ZIP has been created successfully.
        app(AuditLogger::class)->record(
            'course_handbook.package_generated',
            "Course handbook package generated for: {$course->name}",
            $course,
            [
                'service_id' => $course->id,
                'course_name' => $course->name,
                'format' => 'zip',
                'materials_available' => $course->materials->filter(
                    fn ($material) => $material->file
                        && file_exists(storage_path(
                            'app/public/' . $material->file
                        ))
                )->count(),
            ],
            $request
        );

        return response()->download(
            $zipPath,
            $course->name . '-package.zip'
        )->deleteFileAfterSend(true);
    }
}