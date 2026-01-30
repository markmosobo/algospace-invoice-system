<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SystemLog;

class SystemLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Latest logs first
        $systemLogs = SystemLog::orderBy('created_at', 'desc')->get();

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' retrieved system logs'
        ]);

        return response()->json($systemLogs);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth('api')->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        // Validate request
        $request->validate([
            'description' => 'nullable|string|max:255',
            'action' => 'nullable|string|max:100',
        ]);

        $systemLog = SystemLog::create([
            'user_id' => $user->id, // no FK enforcement, just stored
            'action' => $request->action ?? 'SYSTEM_EVENT',
            'description' => $request->description ?? $user->name . ' performed an action',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'message' => 'System log created successfully',
            'systemLog' => $systemLog
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $systemLog = SystemLog::find($id);

        if (!$systemLog) {
            return response()->json([
                'message' => 'System log not found'
            ], 404);
        }

        return response()->json($systemLog);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $systemLog = SystemLog::find($id);

        if (!$systemLog) {
            return response()->json([
                'message' => 'System log not found'
            ], 404);
        }

        $request->validate([
            'description' => 'nullable|string|max:255',
            'action' => 'nullable|string|max:100',
        ]);

        $systemLog->update([
            'description' => $request->description ?? $systemLog->description,
            'action' => $request->action ?? $systemLog->action,
        ]);

        return response()->json([
            'message' => 'System log updated successfully',
            'systemLog' => $systemLog
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $systemLog = SystemLog::find($id);

        if (!$systemLog) {
            return response()->json([
                'message' => 'System log not found'
            ], 404);
        }

        $systemLog->delete();

        return response()->json([
            'message' => 'System log deleted successfully'
        ]);
    }
}
