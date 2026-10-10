<?php

namespace App\Http\Controllers;

use App\Models\ProviderService;
use App\Models\ServiceProvider;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ServiceProviderController extends Controller
{
    protected AuditLogger $auditLogger;

    public function __construct(AuditLogger $auditLogger)
    {
        $this->auditLogger = $auditLogger;
    }

    /**
     * Display service providers and provider services.
     */
    public function index()
    {
        return response()->json([
            'serviceProviders' => ServiceProvider::get(),
            'providerServices' => ProviderService::get(),
        ]);
    }

    /**
     * Create a service provider.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
        ]);

        $serviceProvider = DB::transaction(function () use (
            $validated,
            $request
        ) {
            $serviceProvider = new ServiceProvider();
            $serviceProvider->name = $validated['name'];
            $serviceProvider->phone = $validated['phone'] ?? null;
            $serviceProvider->email = $validated['email'] ?? null;
            $serviceProvider->save();

            $this->auditLogger->record(
                'service_provider.created',
                'Service provider created',
                $serviceProvider,
                [
                    'service_provider_id' => $serviceProvider->id,
                    'name' => $serviceProvider->name,
                ],
                $request,
                auth('api')->id()
            );

            return $serviceProvider;
        });

        return response()->json($serviceProvider, 201);
    }

    /**
     * Display a specific service provider.
     */
    public function show(string $id)
    {
        return response()->json(
            ServiceProvider::findOrFail($id)
        );
    }

    /**
     * Update a service provider.
     */
    public function update(Request $request, ServiceProvider $serviceProvider)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'sometimes|nullable|string|max:30',
            'email' => [
                'sometimes',
                'nullable',
                'email',
                'max:255',
                Rule::unique('service_providers', 'email')
                    ->ignore($serviceProvider->id),
            ],
            'gender' => 'sometimes|nullable|string|max:50',
        ]);

        DB::transaction(function () use (
            $validated,
            $request,
            $serviceProvider
        ) {
            $serviceProvider = ServiceProvider::whereKey($serviceProvider->id)
                ->lockForUpdate()
                ->firstOrFail();

            $fields = ['name', 'phone', 'email', 'gender'];
            $before = $serviceProvider->only($fields);

            foreach ($fields as $field) {
                if (array_key_exists($field, $validated)) {
                    $serviceProvider->{$field} = $validated[$field];
                }
            }

            $serviceProvider->save();

            $after = $serviceProvider->only($fields);
            $changes = [];

            foreach ($after as $field => $value) {
                if (($before[$field] ?? null) != $value) {
                    // Avoid exposing personal contact details in audit properties.
                    if (in_array($field, ['phone', 'email'], true)) {
                        $changes[$field] = 'changed';
                    } else {
                        $changes[$field] = [
                            'old' => $before[$field] ?? null,
                            'new' => $value,
                        ];
                    }
                }
            }

            if (!empty($changes)) {
                $this->auditLogger->record(
                    'service_provider.updated',
                    'Service provider updated',
                    $serviceProvider,
                    [
                        'service_provider_id' => $serviceProvider->id,
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
     * Delete a service provider.
     */
    public function destroy(string $id)
    {
        DB::transaction(function () use ($id) {
            $serviceProvider = ServiceProvider::whereKey($id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->auditLogger->record(
                'service_provider.deleted',
                'Service provider deleted',
                $serviceProvider,
                [
                    'service_provider_id' => $serviceProvider->id,
                    'name' => $serviceProvider->name,
                ],
                request(),
                auth('api')->id()
            );

            $serviceProvider->delete();
        });

        return response()->json([
            'message' => 'Deleted',
        ]);
    }
}