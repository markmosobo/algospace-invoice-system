<?php

namespace App\Http\Controllers;

use App\Models\ProviderService;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProviderServiceController extends Controller
{
    protected AuditLogger $auditLogger;

    public function __construct(AuditLogger $auditLogger)
    {
        $this->auditLogger = $auditLogger;
    }

    /**
     * Display a listing of provider services.
     */
    public function index()
    {
        $providerServices = ProviderService::get();

        return response()->json([
            'providerServices' => $providerServices,
        ]);
    }

    /**
     * Store a newly created provider service.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        $providerService = DB::transaction(function () use ($validated, $request) {
            $providerService = ProviderService::create($validated);

            $this->auditLogger->record(
                'provider_service.created',
                'Provider service created',
                $providerService,
                [
                    'provider_service_id' => $providerService->id,
                    'name' => $providerService->name,
                    'category' => $providerService->category,
                    'price' => $providerService->price,
                ],
                $request,
                auth('api')->id()
            );

            return $providerService;
        });

        return response()->json($providerService, 201);
    }

    /**
     * Display the specified provider service.
     */
    public function show(string $id)
    {
        $providerService = ProviderService::findOrFail($id);

        return response()->json($providerService);
    }

    /**
     * Update the specified provider service.
     */
    public function update(Request $request, ProviderService $providerService)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'sometimes|nullable|string|max:255',
            'price' => 'sometimes|required|numeric|min:0',
        ]);

        DB::transaction(function () use (
            $request,
            $providerService,
            $validated
        ) {
            $providerService = ProviderService::whereKey($providerService->id)
                ->lockForUpdate()
                ->firstOrFail();

            $before = $providerService->only([
                'name',
                'category',
                'price',
            ]);

            $providerService->fill($validated);
            $providerService->save();

            $after = $providerService->only([
                'name',
                'category',
                'price',
            ]);

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
                    'provider_service.updated',
                    'Provider service updated',
                    $providerService,
                    [
                        'provider_service_id' => $providerService->id,
                        'changes' => $changes,
                    ],
                    $request,
                    auth('api')->id()
                );
            }
        });

        return response()->json([
            'message' => 'Updated',
        ]);
    }

    /**
     * Remove the specified provider service.
     */
    public function destroy(string $id)
    {
        DB::transaction(function () use ($id, request) {
            $providerService = ProviderService::whereKey($id)
                ->lockForUpdate()
                ->firstOrFail();

            $serviceDetails = [
                'provider_service_id' => $providerService->id,
                'name' => $providerService->name,
                'category' => $providerService->category,
            ];

            $this->auditLogger->record(
                'provider_service.deleted',
                'Provider service deleted',
                $providerService,
                $serviceDetails,
                request(),
                auth('api')->id()
            );

            $providerService->delete();
        });

        return response()->json([
            'message' => 'Deleted',
        ]);
    }
}