<?php

namespace App\Http\Controllers;

use App\Models\PersonalCategory;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PersonalCategoryController extends Controller
{
    /**
     * Display a listing of personal categories.
     */
    public function index()
    {
        return response()->json(PersonalCategory::all());
    }

    /**
     * Store a newly created personal category.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $personalCategory = DB::transaction(function () use ($data, $request) {
            $category = PersonalCategory::create($data);

            app(AuditLogger::class)->record(
                'personal_category.created',
                'Personal category created',
                $category,
                [
                    'category_id' => $category->id,
                    'name' => $category->name,
                ],
                $request,
                auth('api')->id()
            );

            return $category;
        });

        return response()->json([
            'message' => 'Personal category created successfully',
            'personalCategory' => $personalCategory,
        ], 201);
    }

    /**
     * Display the specified personal category.
     */
    public function show(string $id)
    {
        $personalCategory = PersonalCategory::find($id);

        if (!$personalCategory) {
            return response()->json([
                'message' => 'Personal category not found',
            ], 404);
        }

        return response()->json($personalCategory);
    }

    /**
     * Update the specified personal category.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $personalCategory = DB::transaction(function () use (
            $data,
            $id,
            $request
        ) {
            $category = PersonalCategory::whereKey($id)
                ->lockForUpdate()
                ->first();

            if (!$category) {
                return null;
            }

            $oldName = $category->name;

            if ($oldName !== $data['name']) {
                $category->name = $data['name'];
                $category->save();

                app(AuditLogger::class)->record(
                    'personal_category.updated',
                    'Personal category updated',
                    $category,
                    [
                        'category_id' => $category->id,
                        'old_name' => $oldName,
                        'new_name' => $category->name,
                    ],
                    $request,
                    auth('api')->id()
                );
            }

            return $category;
        });

        if (!$personalCategory) {
            return response()->json([
                'message' => 'Personal category not found',
            ], 404);
        }

        return response()->json([
            'message' => 'Personal category updated successfully',
            'personalCategory' => $personalCategory,
        ]);
    }

    /**
     * Remove the specified personal category.
     */
    public function destroy(string $id)
    {
        $deleted = DB::transaction(function () use ($id) {
            $category = PersonalCategory::whereKey($id)
                ->lockForUpdate()
                ->first();

            if (!$category) {
                return false;
            }

            app(AuditLogger::class)->record(
                'personal_category.deleted',
                'Personal category deleted',
                $category,
                [
                    'category_id' => $category->id,
                    'name' => $category->name,
                ],
                request(),
                auth('api')->id()
            );

            $category->delete();

            return true;
        });

        if (!$deleted) {
            return response()->json([
                'message' => 'Personal category not found',
            ], 404);
        }

        return response()->json([
            'message' => 'Personal category deleted successfully',
        ]);
    }
}