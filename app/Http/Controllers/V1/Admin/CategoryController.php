<?php
namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'ai_role_id' => 'required|exists:ai_roles,id',
            'name' => 'required|string|max:255',
        ]);

        $category = Category::create([
            'ai_role_id' => $request->ai_role_id,
            'name' => $request->name,
        ]);

        return response()->json([
            'status_code' => 1,
            'data' => $category,
            'message' => 'Category created successfully.'
        ], 200);
    }
    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'status_code' => 2,
                'data' => [],
                'message' => 'Category not found.'
            ], 404);
        }

        $category->delete();

        return response()->json([
            'status_code' => 1,
            'data' => [],
            'message' => 'Category deleted successfully.'
        ], 200);
    }
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10); // Default to 10 items per page
        $search = $request->query('search'); // Get the search query if it exists
        $aiRoleId = $request->query('ai_role_id'); // Get the ai_role_id filter if it exists
    
        // Build the query
        $query = Category::with(['aiRole'])
            ->orderBy('created_at', 'desc');
    
        // Apply ai_role_id filter if provided
        if ($aiRoleId) {
            $query->where('ai_role_id', $aiRoleId);
        }
    
        // Apply search filter if search query is provided
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhereHas('aiRole', function($q) use ($search) {
                      $q->where('name', 'like', '%' . $search . '%');
                  });
            });
        }
    
        // Get paginated results
        $categories = $query->paginate($perPage);
    
        return response()->json([
            'status_code' => 1,
            'data' => $categories->items(),
            'pagination' => [
                'total' => $categories->total(),
                'per_page' => $categories->perPage(),
                'current_page' => $categories->currentPage(),
                'last_page' => $categories->lastPage(),
                'from' => $categories->firstItem(),
                'to' => $categories->lastItem(),
            ],
            'message' => 'Categories retrieved successfully.'
        ], 200);
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'ai_role_id' => 'nullable|exists:ai_roles,id',
            'language_id' => 'nullable|exists:languages,id',
        ]);

        $category = Category::find($id);
        if(!$category) {
            return response()->json([
                'status_code' => 2,
                'message' => 'Category not found'
            ], 404);
        }

        if ($request->has('name')) {
            $category->name = $request->name;
        }

        if ($request->has('ai_role_id')) {
            $category->ai_role_id = $request->ai_role_id;
        }

        if ($request->has('language_id')) {
            $category->language_id = $request->language_id;
        }

        $category->save();

        return response()->json([
            'status_code' => 1,
            'data' => $category,
            'message' => 'Category updated successfully.'
        ], 200);
    }
}

?>