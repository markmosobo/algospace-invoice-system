<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    protected AuditLogger $auditLogger;

    public function __construct(AuditLogger $auditLogger)
    {
        $this->auditLogger = $auditLogger;
    }

    /**
     * Display all users.
     */
    public function index()
    {
        $users = User::get();

        $users->makeHidden(['password', 'remember_token']);

        return response()->json($users);
    }

    /**
     * Display partners.
     */
    public function partners()
    {
        $partners = User::where('role', 'partner')->get();

        $partners->makeHidden(['password', 'remember_token']);

        return response()->json($partners);
    }

    /**
     * Display borrowers.
     */
    public function borrowers()
    {
        $borrowers = User::where('role', 'borrower')->get();

        $borrowers->makeHidden(['password', 'remember_token']);

        return response()->json($borrowers);
    }

    /**
     * Create a basic user account.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = DB::transaction(function () use ($validated, $request) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            $this->auditLogger->record(
                'user.created',
                'User account created',
                $user,
                [
                    'user_id' => $user->id,
                ],
                $request,
                auth('api')->id() ?? auth()->id()
            );

            return $user;
        });

        $user->makeHidden(['password', 'remember_token']);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user,
        ]);
    }

    /**
     * Display a specific user.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);

        $user->makeHidden(['password', 'remember_token']);

        return response()->json($user);
    }

    /**
     * Update a basic user account.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($id),
            ],
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $user = DB::transaction(function () use (
            $validated,
            $request,
            $id
        ) {
            $user = User::whereKey($id)
                ->lockForUpdate()
                ->firstOrFail();

            $changes = [];

            if ($user->name !== $validated['name']) {
                $changes['name'] = 'changed';
            }

            if ($user->email !== $validated['email']) {
                $changes['email'] = 'changed';
            }

            $user->name = $validated['name'];
            $user->email = $validated['email'];

            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
                $changes['password'] = 'changed';
            }

            $user->save();

            if (!empty($changes)) {
                $this->auditLogger->record(
                    'user.updated',
                    'User account details updated',
                    $user,
                    [
                        'user_id' => $user->id,
                        'changes' => $changes,
                    ],
                    $request,
                    auth('api')->id() ?? auth()->id()
                );
            }

            return $user;
        });

        $user->makeHidden(['password', 'remember_token']);

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user,
        ]);
    }

    /**
     * Delete a user account.
     */
    public function destroy(string $id)
    {
        $filePaths = [];

        DB::transaction(function () use ($id, &$filePaths) {
            $user = User::whereKey($id)
                ->lockForUpdate()
                ->firstOrFail();

            // Support both profile-photo fields used in this controller.
            foreach (['profile_photo', 'profile_photo_file'] as $field) {
                $path = $user->{$field};

                if (
                    is_string($path) &&
                    $path !== '' &&
                    !filter_var($path, FILTER_VALIDATE_URL)
                ) {
                    $filePaths[] = $path;
                }
            }

            $this->auditLogger->record(
                'user.deleted',
                'User account deleted',
                $user,
                [
                    'user_id' => $user->id,
                ],
                request(),
                auth('api')->id() ?? auth()->id()
            );

            $user->delete();

            DB::afterCommit(function () use (&$filePaths) {
                foreach (array_unique($filePaths) as $path) {
                    Storage::disk('public')->delete($path);
                }
            });
        });

        return response()->json([
            'message' => 'Deleted',
        ]);
    }

    /**
     * Create a user with membership and profile details.
     */
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:borrower,partner,staff',
            'phone' => 'nullable|string|max:20',
            'dob' => 'nullable|date',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'membership_type' => 'nullable|in:student,staff,public,premium',
            'borrow_limit' => 'nullable|integer|min:1',
            'status' => 'nullable|in:active,pending,suspended',
            'profile_photo_file' =>
                'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
            'profile_photo_url' => 'nullable|url',
        ]);

        $profileFilePath = null;

        if ($request->hasFile('profile_photo_file')) {
            $profileFilePath = $request->file('profile_photo_file')
                ->store('uploads/users', 'public');

            if (!$profileFilePath) {
                return response()->json([
                    'message' => 'Unable to upload the profile file.',
                ], 500);
            }
        }

        try {
            $user = DB::transaction(function () use (
                $validated,
                $profileFilePath,
                $request
            ) {
                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'role' => $validated['role'],
                    'status' => $validated['status'] ?? 'active',
                    'phone' => $validated['phone'] ?? null,
                    'dob' => $validated['dob'] ?? null,
                    'address' => $validated['address'] ?? null,
                    'city' => $validated['city'] ?? null,
                    'postal_code' => $validated['postal_code'] ?? null,
                    'membership_type' =>
                        $validated['membership_type'] ?? 'public',
                    'borrow_limit' => $validated['borrow_limit'] ?? 3,
                    'profile_photo_file' => $profileFilePath,
                    'profile_photo_url' =>
                        $validated['profile_photo_url'] ?? null,
                ]);

                $this->auditLogger->record(
                    'user.created',
                    'User account created with membership details',
                    $user,
                    [
                        'user_id' => $user->id,
                        'role' => $user->role,
                        'status' => $user->status,
                        'membership_type' => $user->membership_type,
                    ],
                    $request,
                    auth('api')->id() ?? auth()->id()
                );

                return $user;
            });
        } catch (\Throwable $e) {
            if ($profileFilePath) {
                Storage::disk('public')->delete($profileFilePath);
            }

            throw $e;
        }

        $user->makeHidden(['password', 'remember_token']);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user,
        ], 201);
    }


    /**
     * Update an existing borrower or membership user.
     */
    public function updateUser(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($id),
            ],
            'phone' => 'nullable|string|max:20',
            'dob' => 'nullable|date',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'membership_type' => 'nullable|in:student,staff,public,premium',
            'borrow_limit' => 'nullable|integer|min:1',
            'status' => 'nullable|in:active,pending,suspended',
            'profile_photo_file' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'profile_photo_url' => 'nullable|url',
        ]);

        $newPhotoPath = null;
        $oldPhotoPath = null;

        // Upload the new file before opening the database transaction.
        if ($request->hasFile('profile_photo_file')) {
            $newPhotoPath = $request->file('profile_photo_file')
                ->store('uploads/users', 'public');

            if (!$newPhotoPath) {
                return response()->json([
                    'message' => 'Unable to upload the profile photo.',
                ], 500);
            }
        }

        try {
            $user = DB::transaction(function () use (
                $validated,
                $request,
                $id,
                $newPhotoPath,
                &$oldPhotoPath
            ) {
                $user = User::whereKey($id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $fields = [
                    'name',
                    'email',
                    'phone',
                    'dob',
                    'address',
                    'city',
                    'postal_code',
                    'membership_type',
                    'borrow_limit',
                    'status',
                    'profile_photo_url',
                ];

                // Capture values before the update for audit change tracking.
                $before = $user->only($fields);

                foreach ($fields as $field) {
                    if (array_key_exists($field, $validated)) {
                        $user->{$field} = $validated[$field];
                    }
                }

                // Save the uploaded file path in the correct database column.
                if ($newPhotoPath) {
                    $oldPhotoPath = $user->profile_photo_file;
                    $user->profile_photo_file = $newPhotoPath;
                }

                $user->save();

                $after = $user->only($fields);
                $changes = [];

                foreach ($after as $field => $value) {
                    if (($before[$field] ?? null) != $value) {
                        // Avoid storing personal contact details in audit properties.
                        $changes[$field] = in_array($field, [
                            'email',
                            'phone',
                            'dob',
                            'address',
                            'city',
                            'postal_code',
                            'profile_photo_url',
                        ], true)
                            ? 'changed'
                            : [
                                'old' => $before[$field] ?? null,
                                'new' => $value,
                            ];
                    }
                }

                if ($newPhotoPath) {
                    $changes['profile_photo_file'] = 'changed';
                }

                if (!empty($changes)) {
                    $this->auditLogger->record(
                        'user.updated',
                        'User membership or profile details updated',
                        $user,
                        [
                            'user_id' => $user->id,
                            'changes' => $changes,
                        ],
                        $request,
                        auth('api')->id() ?? auth()->id()
                    );
                }

                // Remove the old file only after the transaction commits.
                if (
                    $oldPhotoPath &&
                    $newPhotoPath &&
                    $oldPhotoPath !== $newPhotoPath
                ) {
                    DB::afterCommit(function () use ($oldPhotoPath) {
                        Storage::disk('public')->delete($oldPhotoPath);
                    });
                }

                return $user;
            }, 3);
        } catch (\Throwable $e) {
            // If the database update fails, remove the newly uploaded file.
            if ($newPhotoPath) {
                Storage::disk('public')->delete($newPhotoPath);
            }

            throw $e;
        }

        $user->makeHidden(['password', 'remember_token']);

        return response()->json($user);
    }


    /**
     * Change the authenticated user's password.
     */
    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth('api')->user() ?? auth()->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        DB::transaction(function () use ($validated, $request, $user) {
            $lockedUser = User::whereKey($user->id)
                ->lockForUpdate()
                ->firstOrFail();

            $lockedUser->password = Hash::make($validated['password']);
            $lockedUser->save();

            $this->auditLogger->record(
                'user.password_changed',
                'User password changed',
                $lockedUser,
                [
                    'user_id' => $lockedUser->id,
                ],
                $request,
                auth('api')->id() ?? auth()->id() ?? $lockedUser->id
            );
        });

        return response()->json([
            'message' => 'Password changed successfully.',
        ]);
    }


    /**
     * Get audit history for a specific user.
     */
    public function auditLogs(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $logs = \App\Models\AuditLog::query()
            ->with('user:id,name,email')
            ->where(function ($query) use ($user) {
                // Events directly associated with this user record.
                $query->where(function ($q) use ($user) {
                    $q->where(
                        'auditable_type',
                        $user->getMorphClass()
                    )->where('auditable_id', $user->id);
                });

                // Events that identify the affected user in properties.
                $query->orWhere(
                    'properties->user_id',
                    $user->id
                );
            })
            ->latest('created_at')
            ->paginate(20);

        return response()->json($logs);
    }

}