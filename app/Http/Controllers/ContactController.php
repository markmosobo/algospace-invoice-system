<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;

class ContactController extends Controller
{
    /**
     * GET /contacts
     * Show all contacts
     */
    public function index()
    {
        return response()->json(
            Contact::latest()->get()
        );
    }

    /**
     * POST /contacts
     * Store contact message
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string',
        ]);

        Contact::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'message' => $request->message,
            'is_read' => false, // make sure this column exists
        ]);

        return response()->json([
            'message' => 'Message received. We will get back to you soon.'
        ]);
    }

    /**
     * PATCH /contacts/{id}/read
     * Mark as read
     */
    public function markAsRead($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->is_read = true;
        $contact->save();

        return response()->json([
            'message' => 'Marked as read'
        ]);
    }

    /**
     * DELETE /contacts/{id}
     * Delete contact
     */
    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();

        return response()->json([
            'message' => 'Contact deleted successfully'
        ]);
    }
}