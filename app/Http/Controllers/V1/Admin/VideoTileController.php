<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SingleTile;
use App\Models\VideoTile;
use Illuminate\Support\Facades\Storage;

class VideoTileController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'ai_role_id' => 'required|exists:ai_roles,id',
            'language_id' => 'required|exists:languages,id',
            'video_link' => 'required|string|max:255',
            'story_name' => 'required|string|max:255',
            'ranking' => 'nullable|integer'
        ]);

        $videoTile = VideoTile::create([
            'ai_role_id' => $request->ai_role_id,
            'language_id' => $request->language_id,
            'video_link' => $request->video_link,
            'story_name' => $request->story_name,
            'ranking' => $request->ranking
        ]);

        return response()->json([
            'status_code' => 1,
            'data' => $videoTile,
            'message' => 'Video Tile created successfully.'
        ], 201);
    }
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10); // Default to 10 items per page
        $search = $request->query('search'); // Get the search query if it exists
        $aiRoleId = $request->query('ai_role_id');
        $languageId = $request->query('language_id');

        // Build the query
        $query = VideoTile::with(['aiRole', 'language'])
            ->orderBy('ranking', 'asc');

        // Apply search filter if search query is provided
        if ($search) {
            $query->where('video_link', 'like', '%' . $search . '%')
                ->orWhereHas('aiRole', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('language', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
        }
        if ($aiRoleId) {
            $query->where('ai_role_id', $aiRoleId);
        }
        if ($languageId) {
            $query->where('language_id', $languageId);
        }

        // Get paginated results
        $videoTiles = $query->paginate($perPage);

        return response()->json([
            'status_code' => 1,
            'data' => $videoTiles->items(),
            'pagination' => [
                'total' => $videoTiles->total(),
                'per_page' => $videoTiles->perPage(),
                'current_page' => $videoTiles->currentPage(),
                'last_page' => $videoTiles->lastPage(),
                'from' => $videoTiles->firstItem(),
                'to' => $videoTiles->lastItem(),
            ],
            'message' => 'Video Tiles retrieved successfully.'
        ], 200);
    }

    public function destroy($id)
    {
        $videoTile = VideoTile::find($id);

        if (!$videoTile) {
            return response()->json([
                'status_code' => 2,
                'data' => [],
                'message' => 'Video Tile not found.'
            ], 404);
        }

        $videoTile->delete();

        return response()->json([
            'status_code' => 1,
            'data' => [],
            'message' => 'Video Tile deleted successfully.'
        ], 200);
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'ai_role_id' => 'nullable|exists:ai_roles,id',
            'language_id' => 'nullable|exists:languages,id',
            'video_link' => 'nullable|string|max:255',
            'story_name' => 'nullable|string|max:255',
            'ranking' => 'nullable|integer'
        ]);

        $videoTile = VideoTile::find($id);
        if(!$videoTile) {
            return response()->json([
                'status_code' => 2,
                'message' => 'Video Tile not found'
            ], 404);
        }

        if ($request->has('ai_role_id')) {
            $videoTile->ai_role_id = $request->ai_role_id;
        }

        if ($request->has('language_id')) {
            $videoTile->language_id = $request->language_id;
        }

        if ($request->has('video_link')) {
            $videoTile->video_link = $request->video_link;
        }

        if ($request->has('story_name')) {
            $videoTile->story_name = $request->story_name;
        }
        if ($request->has('ranking')) {
            $videoTile->ranking = $request->ranking;
        }

        $videoTile->save();

        return response()->json([
            'status_code' => 1,
            'data' => $videoTile,
            'message' => 'Video Tile updated successfully.'
        ], 200);
    }
}
 
?>