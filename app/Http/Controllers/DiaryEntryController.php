<?php

namespace App\Http\Controllers;

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
        ]);

        // Default date to now if not provided
        $entryDate = $request->entry_date ?? Carbon::now();

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

        $diaryEntry->save();

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

        return response()->json([
            'message' => 'Diary entry deleted successfully'
        ]);
    }
}
