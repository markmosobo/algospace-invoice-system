<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Services\AuditLogger;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    protected AuditLogger $auditLogger;

    public function __construct(AuditLogger $auditLogger)
    {
        $this->auditLogger = $auditLogger;
    }

    /**
     * Display all services.
     */
    public function index()
    {
        return response()->json(Service::get());
    }

    /**
     * Create a service or training course.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'type' => 'sometimes|in:service,course',
            'tier' => 'nullable|string|max:100',
            'schedule_type' => 'nullable|in:saturday,weekday,custom',
            'duration_units' => 'nullable|numeric|min:0.5',
            'session_hours' => 'nullable|numeric|min:0.5',
            'is_bundle' => 'sometimes|boolean',
        ]);

        $service = DB::transaction(function () use ($validated, $request) {
            $type = $validated['category'] === 'Training'
                ? ($validated['type'] ?? 'course')
                : 'service';

            $service = new Service();

            $service->name = $validated['name'];
            $service->category = $validated['category'];
            $service->price = $validated['price'];
            $service->unit = $validated['unit'];
            $service->type = $type;
            $service->tier = $validated['tier'] ?? null;
            $service->schedule_type = $validated['schedule_type'] ?? 'saturday';
            $service->duration_units = $validated['duration_units'] ?? null;
            $service->session_hours = $validated['session_hours'] ?? 1.5;
            $service->is_bundle = $validated['is_bundle'] ?? false;
            $service->save();

            $this->auditLogger->record(
                'service.created',
                'Service created',
                $service,
                [
                    'service_id' => $service->id,
                    'name' => $service->name,
                    'category' => $service->category,
                    'type' => $service->type,
                    'price' => $service->price,
                    'tier' => $service->tier,
                    'is_bundle' => (bool) $service->is_bundle,
                ],
                $request,
                auth('api')->id()
            );

            return $service;
        });

        return response()->json([
            'message' => 'Service created successfully',
            'service' => $service,
        ], 201);
    }

    /**
     * Display a specific service.
     */
    public function show(string $id)
    {
        return response()->json(Service::findOrFail($id));
    }

    /**
     * Display a specific course.
     */
    public function showCourse($id)
    {
        $course = Service::findOrFail($id);

        return response()->json([
            'data' => $course,
        ]);
    }

    /**
     * Update a service or course.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'type' => 'sometimes|in:service,course',
            'tier' => 'sometimes|nullable|string|max:100',
            'schedule_type' => 'sometimes|nullable|in:saturday,weekday,custom',
            'duration_units' => 'sometimes|nullable|numeric|min:0.5',
            'session_hours' => 'sometimes|nullable|numeric|min:0.5',
            'is_bundle' => 'sometimes|boolean',
        ]);

        $service = DB::transaction(function () use (
            $validated,
            $request,
            $id
        ) {
            $service = Service::whereKey($id)
                ->lockForUpdate()
                ->firstOrFail();

            $fields = [
                'name',
                'category',
                'price',
                'unit',
                'type',
                'tier',
                'schedule_type',
                'duration_units',
                'session_hours',
                'is_bundle',
            ];

            $before = $service->only($fields);

            $service->name = $validated['name'];
            $service->category = $validated['category'];
            $service->price = $validated['price'];
            $service->unit = $validated['unit'];

            $service->type = $validated['category'] === 'Training'
                ? ($validated['type'] ?? $service->type ?? 'course')
                : 'service';

            foreach ([
                'tier',
                'schedule_type',
                'duration_units',
                'session_hours',
                'is_bundle',
            ] as $field) {
                if (array_key_exists($field, $validated)) {
                    $service->{$field} = $validated[$field];
                }
            }

            // Preserve refresher-course rules.
            if ($service->tier === 'refresher') {
                $service->duration_units = 1;
                $service->session_hours = 1;
            }

            $service->save();

            $after = $service->only($fields);
            $changes = [];

            foreach ($after as $field => $value) {
                if (($before[$field] ?? null) != $value) {
                    $changes[$field] = [
                        'old' => $before[$field] ?? null,
                        'new' => $value,
                    ];
                }
            }

            if (!empty($changes)) {
                $this->auditLogger->record(
                    'service.updated',
                    'Service updated',
                    $service,
                    [
                        'service_id' => $service->id,
                        'changes' => $changes,
                    ],
                    $request,
                    auth('api')->id()
                );
            }

            return $service;
        });

        return response()->json([
            'message' => 'Service updated successfully',
            'service' => $service,
        ]);
    }

    /**
     * Delete a service.
     */
    public function destroy(string $id)
    {
        DB::transaction(function () use ($id) {
            $service = Service::whereKey($id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->auditLogger->record(
                'service.deleted',
                'Service deleted',
                $service,
                [
                    'service_id' => $service->id,
                    'name' => $service->name,
                    'category' => $service->category,
                    'type' => $service->type,
                ],
                request(),
                auth('api')->id()
            );

            $service->delete();
        });

        return response()->json([
            'message' => 'Deleted',
        ]);
    }

    /**
     * Toggle active status.
     */
    public function toggleActive($id)
    {
        $service = DB::transaction(function () use ($id) {
            $service = Service::whereKey($id)
                ->lockForUpdate()
                ->firstOrFail();

            $oldStatus = (bool) $service->is_active;

            $service->is_active = !$service->is_active;
            $service->save();

            $this->auditLogger->record(
                'service.active_status_toggled',
                'Service active status changed',
                $service,
                [
                    'service_id' => $service->id,
                    'previous_is_active' => $oldStatus,
                    'new_is_active' => (bool) $service->is_active,
                ],
                request(),
                auth('api')->id()
            );

            return $service;
        });

        return response()->json([
            'message' => 'Service status updated',
            'is_active' => $service->is_active,
        ]);
    }

    /**
     * Export all services as PDF.
     */
    public function exportPdf()
    {
        $services = Service::all();

        $grouped = $services->groupBy(function ($service) {
            return $service->category ?? 'Uncategorized';
        });

        $data = [
            'grouped' => $grouped,
            'printDate' => now()->format('d/m/Y'),
        ];

        $pdf = Pdf::loadView('pdf.services', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->download('ALGOSPACE_SERVICES.pdf');
    }

    /**
     * List training courses.
     */
    public function courses()
    {
        $courses = Service::where('category', 'Training')
            ->where('type', 'course')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'data' => $courses,
        ]);
    }

    /**
     * Stream training courses PDF.
     */
    public function pdf()
    {
        $courses = Service::where('category', 'Training')
            ->where('type', 'course')
            ->orderBy('tier')
            ->get();

        $pdf = Pdf::loadView('pdf.courses', compact('courses'));

        return $pdf->stream('AlgoSpace-Training-Courses.pdf');
    }
}