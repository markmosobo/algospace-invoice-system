<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    /**
     * Store contact message (already OK)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string',
        ]);

        Contact::create($request->only([
            'name', 'email', 'phone', 'message'
        ]));

        return response()->json([
            'message' => 'Message received. We will get back to you soon.'
        ]);
    }

    /**
     * Retrieve messages as notifications
     */
    public function index()
    {
        $contacts = Contact::with('replier')->latest()->get();

        return response()->json(
            $contacts->map(function ($c) {
                return [
                    'id' => $c->id,
                    'title' => $c->name . ' (' . $c->email . ')',
                    'email' => $c->email,
                    'message' => $c->message,
                    'type' => 'contact',
                    'read_at' => $c->is_read ? $c->created_at : null,
                    'replied_at' => $c->replied_at,
                    'replied_by' => $c->replier?->name,
                    'created_at' => $c->created_at,
                ];
            })
        );
    }

    /**
     * Mark message as read
     */
    public function markAsRead($id)
    {
        $contact = Contact::findOrFail($id);

        $contact->update([
            'is_read' => true,
        ]);

        return response()->json([
            'message' => 'Marked as read',
        ]);
    }


    public function reply(Request $request, $id)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $contact = Contact::findOrFail($id);

        Mail::raw($request->message, function ($mail) use ($contact, $request) {
            $mail->to($contact->email)
                ->subject($request->subject);
        });

        // Optional audit
        $contact->markAsReplied(auth()->id());

        return response()->json([
            'message' => 'Reply sent successfully',
        ]);
    }
}