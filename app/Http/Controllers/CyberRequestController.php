<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CyberRequest;
use App\Models\Service;
use App\Mail\CyberRequestReceived;
use Illuminate\Support\Facades\Mail;
use App\Jobs\SendCyberRequestEmails;

class CyberRequestController extends Controller
{

    public function create()
    {
        $services = Service::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('submit-job', compact('services'));
    }

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
            'files.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120'
        ]);

        $service = Service::findOrFail($validated['service_id']);

        $cyberRequest = CyberRequest::create([
            'service_id' => $validated['service_id'],

            // 🔥 INHERIT FROM SERVICE (KEY CHANGE)
            'payment_type' => $service->payment_type ?? 'prepay',

            'message' => $validated['message'],
            'delivery_method' => $validated['delivery_method'] ?? null,
            'urgency' => $validated['urgency'] ?? null,

            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],

            'status' => 'pending',

            // default depends on payment type
            'payment_status' => $service->payment_type === 'postpay'
                ? 'pending'
                : 'unpaid',

            'amount' => $validated['amount'] ?? $service->price,
        ]);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('cyber_requests', 'public');

                $cyberRequest->files()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientMimeType(),
                ]);
            }
        }

        SendCyberRequestEmails::dispatch($cyberRequest);

        return response()->json([
            'status' => 'success',
            'message' => 'Request submitted successfully',
            'request_id' => $cyberRequest->id,
        ]);
    }

    public function cyberRequests()
    {
        return CyberRequest::with([
            'files',
            'service',
            'invoice.items',   // 🔥 IMPORTANT
            'invoice.customer' // 🔥 IMPORTANT
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

                // 🔥 FULL INVOICE OBJECT (NO SECOND API CALL NEEDED)
                'invoice_id' => $req->invoice?->id,

                'invoice' => $req->invoice ? [
                    'id' => $req->invoice->id,
                    'invoice_number' => $req->invoice->invoice_number,
                    'total_amount' => $req->invoice->total_amount,
                    'status' => $req->invoice->status,
                    'invoice_date' => $req->invoice->invoice_date,

                    // 🔥 CRITICAL PART
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

        // 🔥 ENFORCE PAYMENT RULES
        if ($request->status === 'processing') {

            if ($cyber->payment_type === 'prepay' && $cyber->payment_status !== 'paid') {
                return response()->json([
                    'message' => 'Client must pay before processing (prepay service).'
                ], 422);
            }
        }

        // update fields
        $cyber->fill($request->only([
            'status',
            'payment_status',
            'payment_reference',
            'amount'
        ]));

        // mark paid timestamp
        if ($request->payment_status === 'paid' && !$cyber->paid_at) {
            $cyber->paid_at = now();
        }

        $cyber->save();

        $this->triggerNotifications($cyber, $oldStatus, $oldPayment);

        return response()->json([
            'message' => 'Updated successfully',
            'data' => $cyber
        ]);
    }

    private function triggerNotifications($cyber, $oldStatus, $oldPayment)
    {
        if ($cyber->status !== $oldStatus) {

            if ($cyber->status === 'processing') {
                $this->notifyClient(
                    $cyber,
                    "We have started working on your request: {$cyber->service}"
                );
            }

            if ($cyber->status === 'completed') {
                $this->notifyClient(
                    $cyber,
                    "Your request is complete. You can now collect/download it."
                );
            }

            if ($cyber->status === 'cancelled') {
                $this->notifyClient(
                    $cyber,
                    "Your request has been cancelled. Contact us for clarification."
                );
            }
        }

        if ($cyber->payment_status !== $oldPayment && $cyber->payment_status === 'paid') {
            $this->notifyClient(
                $cyber,
                "Payment received successfully. We are now processing your request."
            );
        }
    }

    private function notifyClient($cyber, $message)
    {
        \Log::info("Notify {$cyber->phone}: {$message}");
    }
}
