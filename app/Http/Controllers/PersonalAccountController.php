<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PersonalAccount;

class PersonalAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $personalAccounts = PersonalAccount::all();
        return response()->json($personalAccounts);        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'name' => 'required|string|max:255',
            'balance' => 'required|numeric|min:0',
            'currency' => 'required|string|max:50',
        ]);

        // Create the personal account
        $personalAccount = PersonalAccount::create([
            'name' => $request->name,
            'balance' => $request->balance,
            'currency' => $request->currency,
        ]);

        return response()->json([
            'message' => 'Personal account created successfully',
            'personalAccount' => $personalAccount
        ], 201);        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $personalAccount = PersonalAccount::find($id);

        if (!$personalAccount) {
            return response()->json([
                'message' => 'Personal account not found'
            ], 404);
        }

        return response()->json($personalAccount);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $personalAccount = PersonalAccount::find($id);

        if (!$personalAccount) {
            return response()->json([
                'message' => 'Personal account not found'
            ], 404);
        }

        // Validate request
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'balance' => 'sometimes|required|numeric|min:0',
            'currency' => 'sometimes|required|string|max:50',
        ]);

        // Update fields if provided
        if ($request->has('name')) {
            $personalAccount->name = $request->name;
        }
        if ($request->has('balance')) {
            $personalAccount->balance = $request->balance;
        }
        if ($request->has('currency')) {
            $personalAccount->currency = $request->currency;
        }

        $personalAccount->save();

        return response()->json([
            'message' => 'Personal account updated successfully',
            'personalAccount' => $personalAccount
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $personalAccount = PersonalAccount::find($id);

        if (!$personalAccount) {
            return response()->json([
                'message' => 'Personal account not found'
            ], 404);
        }

        $personalAccount->delete();

        return response()->json([
            'message' => 'Personal account deleted successfully'
        ]);
    }
}
