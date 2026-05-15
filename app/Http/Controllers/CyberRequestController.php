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
            'files.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120'
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

        // handle files
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

        // dispatch emails to queue
        SendCyberRequestEmails::dispatch($cyberRequest);

        return response()->json([
            'status' => 'success',
            'message' => 'Request submitted successfully',
            'request_id' => $cyberRequest->id,
        ]);
    }

    public function cyberRequests()
    {
        return CyberRequest::with('files')
            ->latest()
            ->get()
            ->map(function ($req) {
                return [
                    'id' => $req->id,
                    'name' => $req->name,
                    'email' => $req->email,
                    'phone' => $req->phone,
                    'service' => $req->service,
                    'status' => $req->status,
                    'urgency' => $req->urgency,
                    'message' => $req->message,
                    'created_at' => $req->created_at->format('d/m/Y H:i'),
                    'files' => $req->files
                ];
            });
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled'
        ]);

        $cyberRequest = CyberRequest::findOrFail($id);

        $cyberRequest->update([
            'status' => $request->status
        ]);

        return response()->json([
            'message' => 'Status updated successfully',
            'data' => $cyberRequest
        ]);
    }    
}
