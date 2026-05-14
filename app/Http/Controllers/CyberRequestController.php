<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CyberRequest;
use App\Mail\CyberRequestReceived;
use Illuminate\Support\Facades\Mail;
use App\Jobs\SendCyberRequestEmails;

class CyberRequestController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'service' => 'required|string',
            'message' => 'required|string',
            'delivery_method' => 'nullable|string',
            'urgency' => 'nullable|string',
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
        ]);

        $cyberRequest = CyberRequest::create([
            'service' => $validated['service'],
            'message' => $validated['message'],
            'delivery_method' => $validated['delivery_method'] ?? null,
            'urgency' => $validated['urgency'] ?? null,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'status' => 'pending',
        ]);

        // dispatch emails to queue
        SendCyberRequestEmails::dispatch($cyberRequest);

        return response()->json([
            'status' => 'success',
            'message' => 'Request submitted successfully',
            'request_id' => $cyberRequest->id,
        ]);
    }
}
