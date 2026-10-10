<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use App\Services\AuditLogger;

class ContactController extends Controller
{
    /**
     * GET /contacts
     * Show all contacts.
     */
    public function index()
    {
        return response()->json(
            Contact::latest()->get()
        );
    }

    /**
     * POST /contacts
     * Store contact message.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string',
        ]);

        $contact = Contact::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'message' => $data['message'],
            'is_read' => false,
        ]);

        // Audit successful contact submission.
        app(AuditLogger::class)->record(
            'contact.received',
            'A new contact message was received',
            $contact,
            [
                'contact_id' => $contact->id,
                'is_read' => $contact->is_read,
                'submitted_by_authenticated_user' => auth()->check(),
            ],
            $request
        );

        return response()->json([
            'message' => 'Message received. We will get back to you soon.',
        ]);
    }

    /**
     * PATCH /contacts/{id}/read
     * Mark contact message as read.
     */
    public function markAsRead(Request $request, $id)
    {
        $contact = Contact::findOrFail($id);

        // Avoid creating duplicate audit entries if already read.
        if (!$contact->is_read) {
            $contact->is_read = true;
            $contact->save();

            app(AuditLogger::class)->record(
                'contact.marked_read',
                "Contact message marked as read (ID: {$contact->id})",
                $contact,
                [
                    'contact_id' => $contact->id,
                    'previous_is_read' => false,
                    'new_is_read' => true,
                ],
                $request
            );
        }

        return response()->json([
            'message' => 'Marked as read',
        ]);
    }

    /**
     * DELETE /contacts/{id}
     * Delete contact message.
     */
    public function destroy(Request $request, $id)
    {
        $contact = Contact::findOrFail($id);

        $contactId = $contact->id;

        // Audit before deletion while the contact still exists.
        app(AuditLogger::class)->record(
            'contact.deleted',
            "Contact message deleted (ID: {$contactId})",
            $contact,
            [
                'contact_id' => $contactId,
                'was_read' => (bool) $contact->is_read,
            ],
            $request
        );

        $contact->delete();

        return response()->json([
            'message' => 'Contact deleted successfully',
        ]);
    }
}