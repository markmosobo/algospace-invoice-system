<?php

namespace App\Http\Controllers;

use App\Models\SystemLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::get();

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' retrieved users'
        ]);  

        return response()->json($users);
    }

    public function partners()
    {
        $partners = User::where('role', 'partner')->get();

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' retrieved partners'
        ]);  

        return response()->json($partners);
    } 
    
    public function borrowers()
    {
        $borrowers = User::where('role', 'borrower')->get();

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' retrieved borrowers'
        ]);  

        return response()->json($borrowers);
    }    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed', // expects password_confirmation field
        ]);

        // Create user with hashed password
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' created user #'.$user->id
        ]);         

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);
        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Find user or fail
        $user = User::findOrFail($id);

        // Validate incoming request
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6|confirmed', // Optional password change
        ]);

        // Prepare data to update
        $data = [
            'name'  => $request->name,
            'email' => $request->email,
        ];

        // Only update password if provided
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Update the user
        $user->update($data);

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' updated details for user #'.$user->id
        ]);         

        return response()->json([
            'message' => 'User updated successfully',
            'user'    => $user
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $user->delete();

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' deleted user #'.$id
        ]); 

        return response()->json(['message' => 'Deleted']);
    }

    public function storeUser(Request $request)
    {
        // Validate request
        $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|unique:users,email',
            'password'        => 'required|string|min:6',
            'role'            => 'required|in:borrower,partner,staff',
            'phone'           => 'nullable|string|max:20',
            'dob'             => 'nullable|date',
            'address'         => 'nullable|string|max:255',
            'city'            => 'nullable|string|max:100',
            'postal_code'     => 'nullable|string|max:20',
            'membership_type' => 'nullable|in:student,staff,public,premium',
            'borrow_limit'    => 'nullable|integer|min:1',
            'status'          => 'nullable|in:active,pending,suspended',
            'profile_photo_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
            'profile_photo_url'  => 'nullable|url',
        ]);

            // Handle uploaded file if exists
        $profileFilePath = null;
        if ($request->hasFile('profile_photo_file')) {
            $profileFilePath = $request->file('profile_photo_file')
                                    ->store('uploads/users', 'public'); // stored in storage/app/public/uploads/users
        }

            // Create the user
        $user = User::create([
            'name'            => $request->name,
            'email'           => $request->email,
            'password'        => Hash::make($request->password),
            'role'            => $request->role,
            'status'          => $request->status ?? 'active',
            'phone'           => $request->phone,
            'dob'             => $request->dob,
            'address'         => $request->address,
            'city'            => $request->city,
            'postal_code'     => $request->postal_code,
            'membership_type' => $request->membership_type ?? 'public',
            'borrow_limit'    => $request->borrow_limit ?? 3,
            'profile_photo_file' => $profileFilePath,
            'profile_photo_url'  => $request->profile_photo_url,
        ]);

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' created user #'.$user->id
        ]);         

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user
        ]);
    }

        // Update existing borrower
    public function updateUser(Request $request, $id)
    {
        $borrower = User::findOrFail($id);

        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => "required|email|unique:users,email,{$id}",
            'phone'           => 'nullable|string|max:20',
            'dob'             => 'nullable|date',
            'address'         => 'nullable|string|max:255',
            'city'            => 'nullable|string|max:100',
            'postal_code'     => 'nullable|string|max:20',
            'membership_type' => 'nullable|in:student,staff,public,premium',
            'borrow_limit'    => 'nullable|integer|min:1',
            'status'          => 'nullable|in:active,pending,suspended',
            'profile_photo'   => 'nullable|image|max:5120', 
            'profile_photo_url' => 'nullable|url',
        ]);

        // Handle file upload
        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($borrower->profile_photo) {
                Storage::disk('public')->delete($borrower->profile_photo);
            }
            $data['profile_photo'] = $request->file('profile_photo')->store('profiles', 'public');
        }

        $borrower->update($data);

        return response()->json($borrower);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        $user = auth()->user();

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'message' => 'Password changed successfully.'
        ]);
    }   


    
}
