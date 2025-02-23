<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AIRole;
use App\Models\Language;
use App\Models\Video;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $perPage = $request->query('per_page', 10); // Default to 10 items per page
        $aiRoleId = $request->query('ai_role_id'); // Get the ai_role_id filter if provided
    
        // Build the query
        // $query = Video::with('category');
        $query = Video::with(['category.aiRole'])->orderBy('created_at', 'desc');
    
        if ($search) {
            $query->where('video_link', 'like', '%' . $search . '%');
        }
        // Apply ai_role_id filter if provided
        if ($aiRoleId) {
            $query->whereHas('category', function ($query) use ($aiRoleId) {
                $query->where('ai_role_id', $aiRoleId);
            });
        }
        
        // Paginate the results
        $videos = $query->paginate($perPage);
    
        return response()->json([
            'status_code' => 1,
            'data' => $videos->items(),
            'pagination' => [
                'total' => $videos->total(),
                'per_page' => $videos->perPage(),
                'current_page' => $videos->currentPage(),
                'last_page' => $videos->lastPage(),
                'from' => $videos->firstItem(),
                'to' => $videos->lastItem(),
            ],
            'message' => 'Videos retrieved successfully.'
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'video_link' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
        ]);
    
        // Extract the video ID from the YouTube link
        $videoId = $this->extractYouTubeVideoId($request->video_link);
    
        if (!$videoId) {
            return response()->json([
                'status_code' => 2,
                'message' => 'Invalid YouTube link provided.'
            ], 400);
        }
    
        // Fetch video details from YouTube
        $videoDetails = $this->fetchYouTubeVideoDetails($videoId);
    
        if (!$videoDetails) {
            return response()->json([
                'status_code' => 3,
                'message' => 'Failed to fetch video details from YouTube.'
            ], 500);
        }
    
        // Store the video details in the database
        $video = Video::create([
            'video_link' => $request->video_link,
            'category_id' => $request->category_id,
            'youtube_video_name' => $videoDetails['title'],
            'thumbnail_url' => $videoDetails['thumbnail'],
        ]);
    
        return response()->json([
            'status_code' => 1,
            'data' => $video,
            'message' => 'Video created successfully.'
        ], 201);
    }
    
    private function extractYouTubeVideoId($url)
    {
        // Check for various YouTube URL formats
        if (preg_match('/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:watch\?v=|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $matches)) {
            return $matches[1];
        }
        
        return null;
    }
    
    private function fetchYouTubeVideoDetails($videoId)
    {
        $apiKey = 'AIzaSyDN3cH0XewivjVl_oeYjBxShgsMb3gMefA'; // Store your YouTube API key in the .env file
        $url = "https://www.googleapis.com/youtube/v3/videos?part=snippet&id={$videoId}&key={$apiKey}";
    
        $response = Http::get($url);
    
        if ($response->successful()) {
            $videoData = $response->json();
    
            if (!empty($videoData['items'])) {
                $snippet = $videoData['items'][0]['snippet'];
                return [
                    'title' => $snippet['title'],
                    'thumbnail' => $snippet['thumbnails']['high']['url'],
                ];
            }
        }
    
        return null;
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'video_link' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $video = Video::find($id);
        if(!$video) {
            return response()->json([
                'status_code' => 2,
                'message' => 'Video not found.'
            ], 404);
        }

        if ($request->has('video_link')) {
            $videoId = $this->extractYouTubeVideoId($request->video_link);
    
            if (!$videoId) {
                return response()->json([
                    'status_code' => 2,
                    'message' => 'Invalid YouTube link provided.'
                ], 400);
            }
        
            // Fetch video details from YouTube
            $videoDetails = $this->fetchYouTubeVideoDetails($videoId);
        
            if (!$videoDetails) {
                return response()->json([
                    'status_code' => 3,
                    'message' => 'Failed to fetch video details from YouTube.'
                ], 500);
            }
            
            $video->video_link = $request->video_link;
            $video->youtube_video_name = $videoDetails['title'];
            $video->thumbnail_url = $videoDetails['thumbnail'];
        }

        if ($request->has('category_id')) {
            $video->category_id = $request->category_id;
        }

        $video->save();

        return response()->json([
            'status_code' => 1,
            'data' => $video,
            'message' => 'Video updated successfully.'
        ], 200);
    }

    public function destroy($id)
    {
        $video = Video::findOrFail($id);
        $video->delete();

        return response()->json([
            'status_code' => 1,
            'message' => 'Video deleted successfully.'
        ], 200);
    }
}
