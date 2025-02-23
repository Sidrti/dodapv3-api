<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\ThoughtsCategory;
use Illuminate\Http\Request;

class ThoughtsCategoryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'ai_role_id' => 'required|exists:ai_roles,id',
            'english_name' => 'required|string|max:255',
            'hindi_name' => 'required|string|max:255',
            'hinglish_name' => 'required|string|max:255',
        ]);

        $thoughtCategory = ThoughtsCategory::create([
            'ai_role_id' => $request->ai_role_id,
            'english_name' => $request->english_name,
            'hindi_name' => $request->hindi_name,
            'hinglish_name' => $request->hinglish_name,
        ]);

        return response()->json([
            'status_code' => 1,
            'data' => $thoughtCategory,
            'message' => 'Thought category created successfully.'
        ], 200);
    }

    public function destroy($id)
    {
        $thoughtCategory = ThoughtsCategory::find($id);

        if (!$thoughtCategory) {
            return response()->json([
                'status_code' => 2,
                'data' => [],
                'message' => 'Thought category not found.'
            ], 404);
        }

        $thoughtCategory->delete();

        return response()->json([
            'status_code' => 1,
            'data' => [],
            'message' => 'Thought category deleted successfully.'
        ], 200);
    }

    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10); // Default to 10 items per page
        $search = $request->query('search'); // Get the search query if it exists
        $aiRoleId = $request->query('ai_role_id'); // Get the ai_role_id filter if it exists

        // Build the query
        $query = ThoughtsCategory::with(['aiRole'])
            ->orderBy('created_at', 'desc');

        // Apply ai_role_id filter if provided
        if ($aiRoleId) {
            $query->where('ai_role_id', $aiRoleId);
        }

        // Apply search filter if search query is provided
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('english_name', 'like', '%' . $search . '%')
                  ->orWhere('hindi_name', 'like', '%' . $search . '%')
                  ->orWhere('hinglish_name', 'like', '%' . $search . '%');
            });
        }

        // Get paginated results
        $thoughtCategories = $query->paginate($perPage);

        return response()->json([
            'status_code' => 1,
            'data' => $thoughtCategories->items(),
            'pagination' => [
                'total' => $thoughtCategories->total(),
                'per_page' => $thoughtCategories->perPage(),
                'current_page' => $thoughtCategories->currentPage(),
                'last_page' => $thoughtCategories->lastPage(),
                'from' => $thoughtCategories->firstItem(),
                'to' => $thoughtCategories->lastItem(),
            ],
            'message' => 'Thought categories retrieved successfully.'
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'ai_role_id' => 'nullable|exists:ai_roles,id',
            'english_name' => 'nullable|string|max:255',
            'hindi_name' => 'nullable|string|max:255',
            'hinglish_name' => 'nullable|string|max:255',
        ]);

        $thoughtCategory = ThoughtsCategory::find($id);

        if (!$thoughtCategory) {
            return response()->json([
                'status_code' => 2,
                'message' => 'Thought category not found.'
            ], 404);
        }

        if ($request->has('ai_role_id')) {
            $thoughtCategory->ai_role_id = $request->ai_role_id;
        }

        if ($request->has('english_name')) {
            $thoughtCategory->english_name = $request->english_name;
        }

        if ($request->has('hindi_name')) {
            $thoughtCategory->hindi_name = $request->hindi_name;
        }

        if ($request->has('hinglish_name')) {
            $thoughtCategory->hinglish_name = $request->hinglish_name;
        }

        $thoughtCategory->save();

        return response()->json([
            'status_code' => 1,
            'data' => $thoughtCategory,
            'message' => 'Thought category updated successfully.'
        ], 200);
    }
}
