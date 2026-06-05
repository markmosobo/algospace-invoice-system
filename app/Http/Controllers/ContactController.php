<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'message' => 'required|string',
        ]);

        Contact::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'message' => $request->message,
        ]);

        return response()->json([
            'message' => 'Message received. We will get back to you soon.'
        ]);
    } 
    
    public function index()
    {
        $contacts = Contact::orderBy('created_at', 'desc')->get();

        return response()->json([
            'data' => $contacts
        ]);
    } 
    
    // MARK AS READ
    public function markAsRead($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->is_read = true;
        $contact->save();

        return response()->json([
            'message' => 'Marked as read'
        ]);
    }

    // DELETE (optional but useful)
    public function destroy($id)
    {
        Contact::destroy($id);

        return response()->json([
            'message' => 'Deleted successfully'
        ]);
    }    
}
