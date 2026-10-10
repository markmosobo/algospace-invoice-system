<?php

namespace App\Http\Controllers;

use App\Models\CyberRequest;
use App\Models\Service;
use App\Services\AuditLogger;
use App\Jobs\SendCyberRequestEmails;
use Illuminate\Http\Request;

class CyberRequestController extends Controller
{
    /**
     * Display the cyber request submission form.
     */
    public function create()
    {
        $services = Service::where('is_active', true)
            ->orderBy('name')
            ->get();

        $servicesCategories = Service::select('category')
            ->distinct()
            ->pluck('category');

        return view('submit-job', compact(
            'services',
            'servicesCategories'
        ));
    }

    /**
     * Submit a new cyber request.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'message' => 'required|string',
            'delivery_method' => 'nullable|string',
            'urgency' => 'nullable|string',
            'name' => 'required|string',
            'email' => 'required|email:rfc,dns|max:255',
            'phone' => ['required', 'regex:/^254[0-9]{9}$/'],
            'amount' => 'nullable|numeric|min:0',
            'files' => 'nullable|array',
            'files.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ]);

        $service = Service::findOrFail($validated['service_id']);

        $cyberRequest = CyberRequest::create([
            'service_id' => $validated['service_id'],
            'payment_type' => $service->payment_type ?? 'prepay',
            'message' => $validated['message'],
            'delivery_method' => $validated['delivery_method'] ?? null,
            'urgency' => $validated['urgency'] ?? null,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'status' => 'pending',
            'payment_status' => $service->payment_type === 'postpay'
                ? 'pending'
                : 'unpaid',
            'amount' => $validated['amount'] ?? $service->price,
        ]);

        $filesCount = 0;

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('cyber_requests', 'public');

                $cyberRequest->files()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientMimeType(),
                ]);

                $filesCount++;
            }
        }

        // Audit the request without recording client contact details or message.
        app(AuditLogger::class)->record(
            'cyber_request.created',
            "Cyber request submitted (ID: {$cyberRequest->id})",
            $cyberRequest,
            [
                'request_id' => $cyberRequest->id,
                'service_id' => $cyberRequest->service_id,
                'payment_type' => $cyberRequest->payment_type,
                'payment_status' => $cyberRequest->payment_status,
                'status' => $cyberRequest->status,
                'amount' => $cyberRequest->amount,
                'urgency' => $cyberRequest->urgency,
                'delivery_method' => $cyberRequest->delivery_method,
                'files_count' => $filesCount,
            ],
            $request
        );

        SendCyberRequestEmails::dispatch($cyberRequest);

        return response()->json([
            'status' => 'success',
            'message' => 'Request submitted successfully',
            'request_id' => $cyberRequest->id,
        ]);
    }

    /**
     * Retrieve cyber requests with their related data.
     */
    public function cyberRequests()
    {
        return CyberRequest::with([
            'files',
            'service',
            'invoice.items',
            'invoice.customer',
        ])
            ->latest()
            ->get()
            ->map(function ($req) {
                return [
                    'id' => $req->id,
                    'name' => $req->name,
                    'email' => $req->email,
                    'phone' => $req->phone,

                    'service' => $req->service ? [
                        'id' => $req->service->id,
                        'name' => $req->service->name,
                        'price' => $req->service->price,
                    ] : null,

                    'status' => $req->status,
                    'urgency' => $req->urgency,

                    'payment_type' => $req->payment_type,
                    'payment_status' => $req->payment_status,
                    'amount' => $req->amount,

                    'message' => $req->message,

                    'created_at' => $req->created_at->format('d/m/Y H:i'),
                    'updated_at' => $req->updated_at->format('d/m/Y H:i'),

                    'files' => $req->files,

                    'invoice_id' => $req->invoice?->id,

                    'invoice' => $req->invoice ? [
                        'id' => $req->invoice->id,
                        'invoice_number' => $req->invoice->invoice_number,
                        'total_amount' => $req->invoice->total_amount,
                        'status' => $req->invoice->status,
                        'invoice_date' => $req->invoice->invoice_date,

                        'customer' => $req->invoice->customer ? [
                            'name' => $req->invoice->customer->name,
                            'email' => $req->invoice->customer->email,
                            'phone' => $req->invoice->customer->phone,
                        ] : null,

                        'items' => $req->invoice->items->map(function ($item) {
                            return [
                                'id' => $item->id,
                                'description' => $item->service_name ?? '—',
                                'quantity' => $item->quantity,
                                'unit_price' => $item->unit_price,
                                'line_total' => $item->quantity * $item->unit_price,
                            ];
                        }),
                    ] : null,
                ];
            });
    }

    /**
     * Update request status, payment status, reference, or amount.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'nullable|in:pending,processing,completed,cancelled',
            'payment_status' => 'nullable|in:unpaid,pending,paid,refunded',
            'payment_reference' => 'nullable|string',
            'amount' => 'nullable|numeric|min:0',
        ]);

        $cyber = CyberRequest::findOrFail($id);

        $oldStatus = $cyber->status;
        $oldPayment = $cyber->payment_status;

        $fields = [
            'status',
            'payment_status',
            'payment_reference',
            'amount',
        ];

        $before = $cyber->only($fields);

        // Enforce prepayment requirements before processing.
        if ($request->status === 'processing') {
            if (
                $cyber->payment_type === 'prepay' &&
                $cyber->payment_status !== 'paid'
            ) {
                return response()->json([
                    'message' => 'Client must pay before processing (prepay service).',
                ], 422);
            }
        }

        $cyber->fill($request->only($fields));

        if (
            $request->payment_status === 'paid' &&
            !$cyber->paid_at
        ) {
            $cyber->paid_at = now();
        }

        $cyber->save();
        $cyber->refresh();

        $after = $cyber->only($fields);
        $changes = [];

        foreach ($fields as $field) {
            $oldValue = $before[$field] ?? null;
            $newValue = $after[$field] ?? null;

            if ($oldValue != $newValue) {
                // Never put the actual payment reference in the audit log.
                if ($field === 'payment_reference') {
                    $changes[$field] = [
                        'changed' => true,
                    ];
                } else {
                    $changes[$field] = [
                        'old' => $oldValue,
                        'new' => $newValue,
                    ];
                }
            }
        }

        if (!empty($changes)) {
            app(AuditLogger::class)->record(
                'cyber_request.updated',
                "Cyber request updated (ID: {$cyber->id})",
                $cyber,
                [
                    'request_id' => $cyber->id,
                    'service_id' => $cyber->service_id,
                    'changes' => $changes,
                    'status_changed' => $oldStatus !== $cyber->status,
                    'payment_status_changed' => $oldPayment !== $cyber->payment_status,
                ],
                $request
            );
        }

        $this->triggerNotifications(
            $cyber,
            $oldStatus,
            $oldPayment
        );

        return response()->json([
            'message' => 'Updated successfully',
            'data' => $cyber,
        ]);
    }

    /**
     * Trigger client notifications after status or payment changes.
     */
    private function triggerNotifications($cyber, $oldStatus, $oldPayment)
    {
        if ($cyber->status !== $oldStatus) {
            if ($cyber->status === 'processing') {
                $serviceName = $cyber->service?->name ?? 'your request';

                $this->notifyClient(
                    $cyber,
                    "We have started working on your request: {$serviceName}"
                );
            }

            if ($cyber->status === 'completed') {
                $this->notifyClient(
                    $cyber,
                    'Your request is complete. You can now collect/download it.'
                );
            }

            if ($cyber->status === 'cancelled') {
                $this->notifyClient(
                    $cyber,
                    'Your request has been cancelled. Contact us for clarification.'
                );
            }
        }

        if (
            $cyber->payment_status !== $oldPayment &&
            $cyber->payment_status === 'paid'
        ) {
            $this->notifyClient(
                $cyber,
                'Payment received successfully. We are now processing your request.'
            );
        }
    }

    /**
     * Current notification placeholder.
     */
    private function notifyClient($cyber, $message)
    {
        \Log::info("Notify {$cyber->phone}: {$message}");
    }
}