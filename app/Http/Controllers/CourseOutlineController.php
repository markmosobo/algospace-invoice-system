<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\CourseOutline;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class CourseOutlineController extends Controller
{
    /**
     * Show the outline for a course.
     */
    public function show(Service $service)
    {
        $outline = CourseOutline::with('items')
            ->where('service_id', $service->id)
            ->first();

        return response()->json([
            'success' => true,
            'data' => $outline,
        ]);
    }

    /**
     * Create a course outline and its items.
     */
    public function store(Request $request, Service $service)
    {
        $validated = $request->validate([
            'overview' => 'nullable|string',
            'certificate_information' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.section' => 'required|string',
            'items.*.title' => 'required|string',
            'items.*.description' => 'nullable|string',
            'items.*.sort_order' => 'nullable|integer',
        ]);

        $outline = DB::transaction(function () use ($validated, $service) {
            $outline = CourseOutline::create([
                'service_id' => $service->id,
                'overview' => $validated['overview'] ?? null,
                'certificate_information' =>
                    $validated['certificate_information'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] ?? [] as $item) {
                $outline->items()->create([
                    'section' => $item['section'],
                    'title' => $item['title'],
                    'description' => $item['description'] ?? null,
                    'sort_order' => $item['sort_order'] ?? 1,
                ]);
            }

            return $outline->load('items');
        });

        app(AuditLogger::class)->record(
            'course_outline.created',
            "Course outline created for: {$service->name}",
            $outline,
            [
                'outline_id' => $outline->id,
                'service_id' => $service->id,
                'course_name' => $service->name,
                'items_count' => $outline->items->count(),
            ],
            $request
        );

        return response()->json([
            'success' => true,
            'message' => 'Course outline created',
            'data' => $outline,
        ], 201);
    }

    /**
     * Update an outline and optionally replace its items.
     */
    public function update(Request $request, CourseOutline $outline)
    {
        $validated = $request->validate([
            'overview' => 'sometimes|nullable|string',
            'certificate_information' => 'sometimes|nullable|string',
            'notes' => 'sometimes|nullable|string',
            'items' => 'sometimes|array',
            'items.*.section' => 'required|string',
            'items.*.title' => 'required|string',
            'items.*.description' => 'nullable|string',
            'items.*.sort_order' => 'nullable|integer',
        ]);

        $before = $outline->only([
            'overview',
            'certificate_information',
            'notes',
        ]);

        $itemsBefore = $outline->items()
            ->get(['id', 'section', 'title', 'description', 'sort_order'])
            ->toArray();

        $outline = DB::transaction(function () use (
            $validated,
            $request,
            $outline
        ) {
            $updates = [];

            foreach ([
                'overview',
                'certificate_information',
                'notes',
            ] as $field) {
                if (array_key_exists($field, $validated)) {
                    $updates[$field] = $validated[$field];
                }
            }

            if (!empty($updates)) {
                $outline->update($updates);
            }

            // Replace items only when the request includes "items".
            if ($request->exists('items')) {
                $outline->items()->delete();

                foreach ($validated['items'] ?? [] as $item) {
                    $outline->items()->create([
                        'section' => $item['section'],
                        'title' => $item['title'],
                        'description' => $item['description'] ?? null,
                        'sort_order' => $item['sort_order'] ?? 1,
                    ]);
                }
            }

            return $outline->fresh()->load('items');
        });

        $after = $outline->only([
            'overview',
            'certificate_information',
            'notes',
        ]);

        $itemsAfter = $outline->items
            ->map(fn ($item) => $item->only([
                'id',
                'section',
                'title',
                'description',
                'sort_order',
            ]))
            ->toArray();

        $changes = [];

        foreach ($after as $field => $value) {
            if (($before[$field] ?? null) != $value) {
                $changes[$field] = [
                    'old' => $before[$field] ?? null,
                    'new' => $value,
                ];
            }
        }

        if ($request->exists('items') && $itemsBefore != $itemsAfter) {
            $changes['items_replaced'] = true;
            $changes['previous_items_count'] = count($itemsBefore);
            $changes['current_items_count'] = count($itemsAfter);
        }

        if (!empty($changes)) {
            app(AuditLogger::class)->record(
                'course_outline.updated',
                "Course outline updated (ID: {$outline->id})",
                $outline,
                [
                    'outline_id' => $outline->id,
                    'service_id' => $outline->service_id,
                    'changes' => $changes,
                ],
                $request
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Outline updated',
            'data' => $outline,
        ]);
    }

    /**
     * Delete a course outline.
     */
    public function destroy(Request $request, CourseOutline $outline)
    {
        $details = [
            'outline_id' => $outline->id,
            'service_id' => $outline->service_id,
            'items_count' => $outline->items()->count(),
        ];

        app(AuditLogger::class)->record(
            'course_outline.deleted',
            "Course outline deleted (ID: {$outline->id})",
            $outline,
            $details,
            $request
        );

        $outline->delete();

        return response()->json([
            'success' => true,
            'message' => 'Deleted',
        ]);
    }

    /**
     * Stream a course outline PDF.
     */
    public function pdf(Request $request, $service)
    {
        $course = Service::with([
            'outline.items',
            'sessions.topics',
        ])->findOrFail($service);

        $pdf = Pdf::loadView(
            'pdf.course-outline',
            compact('course')
        );

        app(AuditLogger::class)->record(
            'course_outline.pdf_generated',
            "Course outline PDF generated for: {$course->name}",
            $course,
            [
                'service_id' => $course->id,
                'course_name' => $course->name,
                'format' => 'pdf',
            ],
            $request
        );

        return $pdf->stream(
            $course->name . '-outline.pdf'
        );
    }
}