<?php

namespace App\Http\Controllers;

use App\Models\SystemLog;
use Illuminate\Http\Request;
use App\Models\DiaryEntry;
use Carbon\Carbon;

class DiaryEntryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $diaryEntries = DiaryEntry::orderBy('entry_date', 'desc')->get();

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' retrieved diary entries'
        ]);

        return response()->json($diaryEntries);        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string|max:50', 
            // note | credit | debit | reminder | event
            'amount' => 'nullable|numeric|min:0',
            'category' => 'nullable|string|max:100',
            'tags' => 'nullable|string|max:255',
            'description' => 'required|string',
            'attachment' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:50',
            'entry_date' => 'nullable|date',
            'remind_at' => 'nullable|date', // <--- added
        ]);

        // Default dates
        $entryDate = $request->entry_date ?? Carbon::now();
        $remindAt = $request->remind_at ?? null; // only set if provided

        // Create the diary entry
        $diaryEntry = DiaryEntry::create([
            'title' => $request->title,
            'type' => $request->type,
            'amount' => $request->amount,
            'category' => $request->category,
            'tags' => $request->tags,
            'description' => $request->description,
            'attachment' => $request->attachment,
            'entry_date' => $entryDate,
            'status' => $request->status ?? 'pending',
            'remind_at' => $remindAt, // <--- added
        ]);

        // Record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' created diary entry #'.$diaryEntry->id
        ]);          

        return response()->json([
            'message' => 'Diary entry created successfully',
            'diaryEntry' => $diaryEntry
        ], 201);          
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $diaryEntry = DiaryEntry::find($id);

        if (!$diaryEntry) {
            return response()->json([
                'message' => 'Diary entry not found'
            ], 404);
        }

        return response()->json($diaryEntry);        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $diaryEntry = DiaryEntry::find($id);

        if (!$diaryEntry) {
            return response()->json([
                'message' => 'Diary entry not found'
            ], 404);
        }

        // Validate input
        $request->validate([
            'title' => 'sometimes|string|max:255',
            'type' => 'sometimes|string|max:50',
            'amount' => 'nullable|numeric|min:0',
            'category' => 'nullable|string|max:100',
            'tags' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'attachment' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:50',
            'entry_date' => 'nullable|date',
            'remind_at' => 'nullable|date', // <--- added
        ]);

        // Update fields safely
        $diaryEntry->title = $request->title ?? $diaryEntry->title;
        $diaryEntry->type = $request->type ?? $diaryEntry->type;
        $diaryEntry->amount = $request->amount ?? $diaryEntry->amount;
        $diaryEntry->category = $request->category ?? $diaryEntry->category;
        $diaryEntry->tags = $request->tags ?? $diaryEntry->tags;
        $diaryEntry->description = $request->description ?? $diaryEntry->description;
        $diaryEntry->attachment = $request->attachment ?? $diaryEntry->attachment;
        $diaryEntry->status = $request->status ?? $diaryEntry->status;
        $diaryEntry->entry_date = $request->entry_date ?? $diaryEntry->entry_date;
        $diaryEntry->remind_at = $request->remind_at ?? $diaryEntry->remind_at; // <--- added

        $diaryEntry->save();

        // Record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' updated diary entry #'.$id
        ]);           

        return response()->json([
            'message' => 'Diary entry updated successfully',
            'diaryEntry' => $diaryEntry
        ]);        
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $diaryEntry = DiaryEntry::find($id);

        if (!$diaryEntry) {
            return response()->json([
                'message' => 'Diary entry not found'
            ], 404);
        }

        $diaryEntry->delete();

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' deleted entry #'.$diaryEntry->id
        ]);           

        return response()->json([
            'message' => 'Diary entry deleted successfully'
        ]);
    }


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
            ->map(function ($r) use ($now, $todayStart, $todayEnd, $tomorrowStart, $tomorrowEnd) {
                $rRemind = $r->remind_at; // now Carbon
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
                    'time' => $rRemind->format('H:i'),
                    'status' => $status
                ];
            })
            ->filter(fn($r) => $r['status'] !== '') // remove anything not in these 3 categories
            ->sortBy(function($r) { // optional: overdue first, then today, then tomorrow
                $order = ['overdue' => 0, 'today' => 1, 'tomorrow' => 2];
                return $order[$r['status']];
            })
            ->values(); // reset keys

        return response()->json($reminders);
    }


    public function markDone($id)
    {
        $diaryEntry = DiaryEntry::find($id);

        if (!$diaryEntry) {
            return response()->json(['message' => 'Diary entry not found'], 404);
        }

        $diaryEntry->status = 'done';
        $diaryEntry->save();

        return response()->json(['message' => 'Marked as done']);
    }


}
