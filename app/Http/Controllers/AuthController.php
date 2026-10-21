<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use App\Mail\VerifyEmailMail;

class AuthController extends Controller
{
    /**
     * Register a new user and return verification instructions.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email|unique:users',
            'password'   => 'required|min:6',
            'phone'      => 'nullable|string|max:20',
            'role'       => 'required|in:office,personal,farm,partner,staff,borrower,client',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            'name'            => trim($request->first_name . ' ' . $request->last_name),
            'email'           => $request->email,
            'password'        => Hash::make($request->password),
            'role'            => $request->role,
            'status'          => 'pending',
            'phone'           => $request->phone,
            'dob'             => $request->dob ?? null,
            'address'         => $request->address ?? null,
            'city'            => $request->city ?? null,
            'postal_code'     => $request->postal_code ?? null,
            'membership_type' => $request->membership_type ?? 'basic',
            'borrow_limit'    => $request->borrow_limit ?? 0,
        ]);

        // Record successful account creation.
        app(AuditLogger::class)->record(
            'user.registered',
            "User account created: {$user->name}",
            $user,
            [
                'role' => $user->role,
                'status' => $user->status,
            ],
            $request,
            $user->id
        );

        // Create a signed email verification link.
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id'   => $user->id,
                'hash' => sha1($user->getEmailForVerification()),
            ]
        );

        // Send the verification email.
        Mail::to($user->email)->send(
            new VerifyEmailMail($verificationUrl)
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Account created. Please verify your email.',
            'user'    => $user,
        ], 201);
    }

    /**
     * Login user and return JWT.
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // Record failed authentication attempts.
        if (!$token = auth('api')->attempt($credentials)) {
            app(AuditLogger::class)->record(
                'auth.login_failed',
                'Login failed: invalid credentials',
                null,
                [
                    'attempted_email' => $request->input('email'),
                    'reason' => 'invalid_credentials',
                ],
                $request
            );

            return response()->json([
                'error' => 'Invalid credentials',
            ], 401);
        }

        $user = auth('api')->user();

        // Require email verification before allowing login.
        if (!$user->hasVerifiedEmail()) {
            app(AuditLogger::class)->record(
                'auth.login_blocked',
                "Login blocked: email not verified for user ID {$user->id}",
                $user,
                [
                    'reason' => 'email_not_verified',
                ],
                $request,
                $user->id
            );

            return response()->json([
                'status' => 'error',
                'message' => 'Please verify your email first',
            ], 403);
        }

        // Record successful login.
        app(AuditLogger::class)->record(
            'auth.login',
            "User logged in: {$user->name}",
            $user,
            [],
            $request,
            $user->id
        );

        return response()->json([
            'status' => 'success',
            'user' => $user,
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ]);
    }

    /**
     * Logout user and invalidate JWT.
     */
    public function logout(Request $request)
    {
        try {
            $user = auth('api')->user();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthenticated',
                ], 401);
            }

            // Capture the user before invalidating the token.
            auth('api')->logout();

            app(AuditLogger::class)->record(
                'auth.logout',
                "User logged out: {$user->name}",
                $user,
                [],
                $request,
                $user->id
            );

            return response()->json([
                'status' => 'success',
                'message' => 'User logged out successfully.',
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'status' => 'error',
                'message' => 'Logout failed',
            ], 500);
        }
    }

    /**
     * Get authenticated user.
     */
    public function me()
    {
        return response()->json([
            'status' => 'success',
            'user' => auth('api')->user(),
        ]);
    }

    /**
     * Refresh authentication token.
     */
    public function refresh(Request $request)
    {
        try {
            $user = auth('api')->user();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthenticated',
                ], 401);
            }

            $token = auth('api')->refresh();

            app(AuditLogger::class)->record(
                'auth.token_refreshed',
                "Authentication token refreshed for user ID {$user->id}",
                $user,
                [],
                $request,
                $user->id
            );

            return response()->json([
                'status' => 'success',
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60,
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'status' => 'error',
                'message' => 'Token refresh failed',
            ], 401);
        }
    }
}