<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SingleTile;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class SingleTileController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'ai_role_id' => 'required|exists:ai_roles,id',
            'language_id' => 'required|exists:languages,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'date' => 'required|date',
            'thoughts_category_id' => 'nullable|exists:thoughts_categories,id',
            'ranking' => 'nullable|integer'
        ]);

        // Check if a SingleTile already exists for the given ai_role_id, language_id, and date
        // $singleTile = SingleTile::where('ai_role_id', $request->ai_role_id)
        //     ->where('language_id', $request->language_id)
        //     ->where('date', $request->date)
        //     ->first();

        // if ($singleTile) {
        //     // If it exists, delete the old image
        //     if ($singleTile->image) {
        //         Storage::disk('public')->delete($singleTile->image);
        //     }
        // } else {
        //     // If it doesn't exist, create a new SingleTile instance
        //     $singleTile = new SingleTile();
        //     $singleTile->ai_role_id = $request->ai_role_id;
        //     $singleTile->language_id = $request->language_id;
        //     $singleTile->date = $request->date;
        //     $singleTile->thoughts_category_id = $request->thoughts_category_id;
        //     $singleTile->ranking = $request->ranking;
        // }

        // Store the new image
        $singleTile = new SingleTile();
        $singleTile->ai_role_id = $request->ai_role_id;
        $singleTile->language_id = $request->language_id;
        $singleTile->date = $request->date;
        $singleTile->thoughts_category_id = $request->thoughts_category_id;
        $singleTile->ranking = $request->ranking;
        $singleTile->image = $request->file('image')->store('images/single_tiles', 'public');

        // Save the SingleTile
        $singleTile->save();

        return response()->json([
            'status_code' => 1,
            'data' => $singleTile,
            'message' => 'Single Tile created successfully.'
        ], 201);
    }
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10); // Default to 10 items per page
        $search = $request->query('search'); // Get the search query if it exists
        $aiRoleId = $request->query('ai_role_id');
        $categoryId = $request->query('category_id'); // Category filter
        $dateFilter = $request->query('date'); // Date filter (format: YYYY-MM-DD)
        $languageId = $request->query('language_id'); // Language filter

        // Build the query
        $query = SingleTile::with(['aiRole', 'language','thoughtsCategory'])
            ->orderBy('ranking', 'asc');

        // Apply search filter if search query is provided
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('aiRole', function($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                })->orWhereHas('language', function($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                })->orWhere('date', 'like', '%' . $search . '%');
            });
        }
        if ($aiRoleId) {
            $query->where('ai_role_id', $aiRoleId);
        }
        if ($categoryId) {
            $query->where('thoughts_category_id', $categoryId);
        }
    
        // Apply date filter if provided
        if ($dateFilter) {
            $query->whereDate('created_at', $dateFilter);
        }
    
        // Apply language filter if provided
        if ($languageId) {
            $query->where('language_id', $languageId);
        }
                // Get paginated results
        $singleTiles = $query->paginate($perPage);
       
        return response()->json([
            'status_code' => 1,
            'data' => $singleTiles->items(),
            'pagination' => [
                'total' => $singleTiles->total(),
                'per_page' => $singleTiles->perPage(),
                'current_page' => $singleTiles->currentPage(),
                'last_page' => $singleTiles->lastPage(),
                'from' => $singleTiles->firstItem(),
                'to' => $singleTiles->lastItem(),
            ],
            'message' => 'Single Tiles retrieved successfully.'
        ], 200);
    }

    public function destroy($id)
    {
        $singleTile = SingleTile::find($id);

        if (!$singleTile) {
            return response()->json([
                'status_code' => 2,
                'data' => [],
                'message' => 'Single Tile not found.'
            ], 404);
        }

        // Delete the image from storage
        if ($singleTile->image) {
            Storage::disk('public')->delete($singleTile->image);
        }

        // Delete the single tile
        $singleTile->delete();

        return response()->json([
            'status_code' => 1,
            'data' => [],
            'message' => 'Single Tile deleted successfully.'
        ], 200);
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'ai_role_id' => 'nullable|exists:ai_roles,id',
            'language_id' => 'nullable|exists:languages,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'date' => 'nullable|date',
            'thoughts_category_id' => 'nullable|exists:thoughts_categories,id',
            'ranking' => 'nullable|integer'
        ]);

        $singleTile = SingleTile::find($id);
        if(!$singleTile) {
            return response()->json([
                'status_code' => 2,
                'message' => 'Single Tile not found'
            ], 404);
        }

        if ($request->has('ai_role_id')) {
            $singleTile->ai_role_id = $request->ai_role_id;
        }

        if ($request->has('language_id')) {
            $singleTile->language_id = $request->language_id;
        }

        if ($request->has('date')) {
            $singleTile->date = $request->date;
        }

        if ($request->has('thoughts_category_id')) {
            $singleTile->thoughts_category_id = $request->thoughts_category_id;
        }

        if ($request->has('ranking')) {
            $singleTile->ranking = $request->ranking;
        }

        if ($request->hasFile('image')) {
            // Store the new image and update the path in the database
            $singleTile->image = $request->file('image')->store('images/single_tiles', 'public');
        }

        $singleTile->save();

        return response()->json([
            'status_code' => 1,
            'data' => $singleTile,
            'message' => 'Single Tile updated successfully.'
        ], 200);
    }
}
 
?>