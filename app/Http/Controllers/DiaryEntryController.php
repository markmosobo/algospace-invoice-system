<?php

namespace App\Http\Controllers;

use App\Models\DiaryEntry;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DiaryEntryController extends Controller
{
    /**
     * Display a listing of diary entries.
     */
    public function index()
    {
        $diaryEntries = DiaryEntry::orderBy('entry_date', 'desc')->get();

        return response()->json($diaryEntries);
    }

    /**
     * Store a newly created diary entry.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'amount' => 'nullable|numeric|min:0',
            'category' => 'nullable|string|max:100',
            'tags' => 'nullable|string|max:255',
            'description' => 'required|string',
            'attachment' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:50',
            'entry_date' => 'nullable|date',
            'remind_at' => 'nullable|date',
        ]);

        $diaryEntry = DiaryEntry::create([
            'title' => $validated['title'],
            'type' => $validated['type'],
            'amount' => $validated['amount'] ?? null,
            'category' => $validated['category'] ?? null,
            'tags' => $validated['tags'] ?? null,
            'description' => $validated['description'],
            'attachment' => $validated['attachment'] ?? null,
            'entry_date' => $validated['entry_date'] ?? Carbon::now(),
            'status' => $validated['status'] ?? 'pending',
            'remind_at' => $validated['remind_at'] ?? null,
        ]);

        app(AuditLogger::class)->record(
            'diary_entry.created',
            "Diary entry created (ID: {$diaryEntry->id})",
            $diaryEntry,
            [
                'diary_entry_id' => $diaryEntry->id,
                'type' => $diaryEntry->type,
                'category' => $diaryEntry->category,
                'status' => $diaryEntry->status,
                'amount' => $diaryEntry->amount,
                'has_reminder' => !empty($diaryEntry->remind_at),
                'has_attachment' => !empty($diaryEntry->attachment),
            ],
            $request,
            auth('api')->id()
        );

        return response()->json([
            'message' => 'Diary entry created successfully',
            'diaryEntry' => $diaryEntry,
        ], 201);
    }

    /**
     * Display a specific diary entry.
     */
    public function show(string $id)
    {
        $diaryEntry = DiaryEntry::find($id);

        if (!$diaryEntry) {
            return response()->json([
                'message' => 'Diary entry not found',
            ], 404);
        }

        return response()->json($diaryEntry);
    }

    /**
     * Update an existing diary entry.
     */
    public function update(Request $request, string $id)
    {
        $diaryEntry = DiaryEntry::find($id);

        if (!$diaryEntry) {
            return response()->json([
                'message' => 'Diary entry not found',
            ], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'type' => 'sometimes|string|max:50',
            'amount' => 'sometimes|nullable|numeric|min:0',
            'category' => 'sometimes|nullable|string|max:100',
            'tags' => 'sometimes|nullable|string|max:255',
            'description' => 'sometimes|nullable|string',
            'attachment' => 'sometimes|nullable|string|max:255',
            'status' => 'sometimes|nullable|string|max:50',
            'entry_date' => 'sometimes|nullable|date',
            'remind_at' => 'sometimes|nullable|date',
        ]);

        $fieldsToAudit = [
            'title',
            'type',
            'amount',
            'category',
            'tags',
            'description',
            'attachment',
            'status',
            'entry_date',
            'remind_at',
        ];

        $before = $diaryEntry->only($fieldsToAudit);

        // Only update fields actually provided in the request.
        $diaryEntry->fill($validated);
        $diaryEntry->save();

        $changes = [];

        foreach ($fieldsToAudit as $field) {
            $oldValue = $before[$field] ?? null;
            $newValue = $diaryEntry->getAttribute($field);

            if ($oldValue != $newValue) {
                // Avoid copying diary descriptions or attachment paths into audit logs.
                if ($field === 'description' || $field === 'attachment') {
                    $changes[$field] = ['changed' => true];
                } else {
                    $changes[$field] = [
                        'old' => $oldValue,
                        'new' => $newValue,
                    ];
                }
            }
        }

        if (!empty($changes)) {
            app(AuditLogger::class)->record(
                'diary_entry.updated',
                "Diary entry updated (ID: {$diaryEntry->id})",
                $diaryEntry,
                [
                    'diary_entry_id' => $diaryEntry->id,
                    'changes' => $changes,
                ],
                $request,
                auth('api')->id()
            );
        }

        return response()->json([
            'message' => 'Diary entry updated successfully',
            'diaryEntry' => $diaryEntry,
        ]);
    }

    /**
     * Delete a diary entry.
     */
    public function destroy(string $id)
    {
        $diaryEntry = DiaryEntry::find($id);

        if (!$diaryEntry) {
            return response()->json([
                'message' => 'Diary entry not found',
            ], 404);
        }

        $entryId = $diaryEntry->id;
        $entryType = $diaryEntry->type;

        app(AuditLogger::class)->record(
            'diary_entry.deleted',
            "Diary entry deleted (ID: {$entryId})",
            $diaryEntry,
            [
                'diary_entry_id' => $entryId,
                'type' => $entryType,
            ],
            request(),
            auth('api')->id()
        );

        $diaryEntry->delete();

        return response()->json([
            'message' => 'Diary entry deleted successfully',
        ]);
    }

    /**
     * Get overdue, today's, and tomorrow's reminders.
     */
    public function remindersOverview()
    {
        $now = now();
        $todayStart = Carbon::today()->startOfDay();
        $todayEnd = Carbon::today()->endOfDay();
        $tomorrowStart = Carbon::tomorrow()->startOfDay();
        $tomorrowEnd = Carbon::tomorrow()->endOfDay();

        $reminders = DiaryEntry::where('type', 'reminder')
            ->where('status', 'pending')
            ->whereNotNull('remind_at')
            ->get()
            ->map(function ($r) use (
                $now,
                $todayStart,
                $todayEnd,
                $tomorrowStart,
                $tomorrowEnd
            ) {
                $rRemind = $r->remind_at;
                $status = '';

                if ($rRemind < $now) {
                    $status = 'overdue';
                } elseif ($rRemind->between($todayStart, $todayEnd)) {
                    $status = 'today';
                } elseif ($rRemind->between($tomorrowStart, $tomorrowEnd)) {
                    $status = 'tomorrow';
                }

                return [
                    'id' => $r->id,
                    'title' => $r->title,
                    'remind_at' => $rRemind,
                    'date' => $rRemind->format('d/m/Y'),
                    'time' => $rRemind->format('H:i'),
                    'status' => $status,
                ];
            })
            ->filter(fn ($r) => $r['status'] !== '')
            ->sortBy(function ($r) {
                $order = [
                    'overdue' => 0,
                    'today' => 1,
                    'tomorrow' => 2,
                ];

                return $order[$r['status']];
            })
            ->values();

        return response()->json($reminders);
    }

    /**
     * Mark a diary entry as done.
     */
    public function markDone($id)
    {
        $diaryEntry = DiaryEntry::find($id);

        if (!$diaryEntry) {
            return response()->json([
                'message' => 'Diary entry not found',
            ], 404);
        }

        $oldStatus = $diaryEntry->status;

        $diaryEntry->status = 'done';
        $diaryEntry->save();

        if ($oldStatus !== 'done') {
            app(AuditLogger::class)->record(
                'diary_entry.completed',
                "Diary entry marked as done (ID: {$diaryEntry->id})",
                $diaryEntry,
                [
                    'diary_entry_id' => $diaryEntry->id,
                    'previous_status' => $oldStatus,
                    'new_status' => 'done',
                ],
                request(),
                auth('api')->id()
            );
        }

        return response()->json([
            'message' => 'Marked as done',
        ]);
    }
}