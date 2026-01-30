<?php

namespace App\Http\Controllers;

use App\Models\SystemLog;
use Illuminate\Http\Request;
use App\Models\PersonalCategory;

class PersonalCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $personalCategories = PersonalCategory::all();

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' retrieved personal categories'
        ]);

        return response()->json($personalCategories);         
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Create the personal category
        $personalCategory = PersonalCategory::create([
            'name' => $request->name
        ]);

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' created personal category #'.$personalCategory->id
        ]);        

        return response()->json([
            'message' => 'Personal category created successfully',
            'personalCategory' => $personalCategory
        ], 201);         
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $personalCategory = PersonalCategory::find($id);

        if (!$personalCategory) {
            return response()->json([
                'message' => 'Personal category not found'
            ], 404);
        }

        return response()->json($personalCategory);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $personalCategory = PersonalCategory::find($id);

        if (!$personalCategory) {
            return response()->json([
                'message' => 'Personal category not found'
            ], 404);
        }

        // Validate request
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Update category
        $personalCategory->name = $request->name;
        $personalCategory->save();

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' created personal category #'.$personalCategory->id
        ]);        

        return response()->json([
            'message' => 'Personal category updated successfully',
            'personalCategory' => $personalCategory
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $personalCategory = PersonalCategory::find($id);

        if (!$personalCategory) {
            return response()->json([
                'message' => 'Personal category not found'
            ], 404);
        }

        $personalCategory->delete();

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' deleted personal category #'.$id
        ]);        

        return response()->json([
            'message' => 'Personal category deleted successfully'
        ]);
    }
}
