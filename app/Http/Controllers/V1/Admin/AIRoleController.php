<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AIRole;
use App\Models\Language;
use Illuminate\Support\Facades\Storage;

class AiRoleController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'hindi_name' => 'required|string|max:255',
            'description' => 'required|string',
            'image1' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'image2' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'image3' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'prompt1' => 'required|string',
            'prompt2' => 'required|string',
            'prompt3' => 'required|string',
            'sound' => 'nullable|mimes:mp3,wav|max:10240',
            'color' => 'required|string|max:7',
            'language_ids' => 'required|array',
            'language_texts' => 'required|array',
            'question_texts' => 'required|array',
        ]);

        $aiRole = new AIRole();
        $aiRole->name = $request->name;
        $aiRole->hindi_name = $request->hindi_name;
        $aiRole->description = $request->description;
        $aiRole->prompt1 = $request->prompt1;
        $aiRole->prompt2 = $request->prompt2;
        $aiRole->prompt3 = $request->prompt3;
        $aiRole->color = $request->color;

        if ($request->hasFile('image1')) {
            $aiRole->image1 = $request->file('image1')->store('images/airole', 'public');
        }

        if ($request->hasFile('image2')) {
            $aiRole->image2 = $request->file('image2')->store('images/airole', 'public');
        }

        if ($request->hasFile('image3')) {
            $aiRole->image3 = $request->file('image3')->store('images/airole', 'public');
        }

        if ($request->hasFile('sound')) {
            $aiRole->sound = $request->file('sound')->store('sounds/airole', 'public');
        }
        
        $aiRole->save();

        $languageIds = $request->input('language_ids', []);
        $languageTexts = $request->input('language_texts', []);
        $questionTexts = $request->input('question_texts', []);

        foreach ($languageIds as $index => $languageId) {
            if ($languageId) {
                $aiRole->languages()->attach($languageId, [
                    'language_text' => $languageTexts[$index] ?? null,
                    'question_text' => $questionTexts[$index] ?? null,
                ]);
            }
        }

        return response()->json([
            'status_code' => 1,
            'data' => $aiRole->load('languages'),
            'message' => 'AI Role created successfully.'
        ], 200);
    }
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10); // Default to 3 items per page
        $search = $request->query('search'); // Get the search query if it exists

        // Build the query
        $query = AIRole::with('languages');

        // Apply search filter if search query is provided
        if ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                ->orWhereHas('languages', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
        }

        // Get paginated results
        $aiRoles = $query->paginate($perPage);

        return response()->json([
            'status_code' => 1,
            'data' => $aiRoles->items(),
            'pagination' => [
                'total' => $aiRoles->total(),
                'per_page' => $aiRoles->perPage(),
                'current_page' => $aiRoles->currentPage(),
                'last_page' => $aiRoles->lastPage(),
                'from' => $aiRoles->firstItem(),
                'to' => $aiRoles->lastItem(),
            ],
            'message' => 'AI Roles retrieved successfully.'
        ], 200);
    }
    public function destroy($id)
    {
        $aiRole = AIRole::find($id);

        if (!$aiRole) {
            return response()->json([
                'status_code' => 2,
                'data' => [],
                'message' => 'AI Role not found.'
            ], 404);
        }

        $aiRole->languages()->detach(); // Detach associated languages
        $aiRole->delete(); // Delete the AI role

        return response()->json([
            'status_code' => 1,
            'data' => [],
            'message' => 'AI Role deleted successfully.'
        ], 200);
    }
    public function fetchLanguage(Request $request)
    {
        $language = Language::get();

        return response()->json([
            'status_code' => 1,
            'data' => $language,
            'message' => 'language retrieved successfully.'
        ], 200);
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'sometimes|nullable|string|max:255',
            'hindi_name' => 'sometimes|nullable|string|max:255',
            'description' => 'sometimes|nullable|string',
            'status' => 'sometimes|nullable|string',
            'image1' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'image2' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'image3' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'prompt1' => 'sometimes|nullable|string',
            'prompt2' => 'sometimes|nullable|string',
            'prompt3' => 'sometimes|nullable|string',
            'color' => 'sometimes|nullable|string|max:7',
            'language_ids' => 'sometimes|nullable|array',
            'language_texts' => 'sometimes|nullable|array',
            'question_texts' => 'sometimes|nullable|array',
            'sound' => 'sometimes|nullable|mimes:mp3,wav|max:10240',
        ]);

        $aiRole = AIRole::where('id',$id)->first();
        if(!$aiRole) {
            return response()->json([
                'status_code' => 2,
                'message' => 'AI Role not found'
            ], 404);
        }
        if ($request->has('name')) {
            $aiRole->name = $request->name;
        }

        if ($request->has('description')) {
            $aiRole->description = $request->description;
        }

        if ($request->has('status')) {
            $aiRole->status = $request->status;
        }
        if ($request->has('prompt1')) {
            $aiRole->prompt1 = $request->prompt1;
        }

        if ($request->has('prompt2')) {
            $aiRole->prompt2 = $request->prompt2;
        }

        if ($request->has('prompt3')) {
            $aiRole->prompt3 = $request->prompt3;
        }

        if ($request->has('color')) {
            $aiRole->color = $request->color;
        }

        if ($request->has('hindi_name')) {
            $aiRole->hindi_name = $request->hindi_name;
        }

        if ($request->hasFile('image1')) {
            $aiRole->image1 = $request->file('image1')->store('images/airole', 'public');
        }

        if ($request->hasFile('image2')) {
            $aiRole->image2 = $request->file('image2')->store('images/airole', 'public');
        }

        if ($request->hasFile('image3')) {
            $aiRole->image3 = $request->file('image3')->store('images/airole', 'public');
        }

        if ($request->hasFile('sound')) {
            $aiRole->sound = $request->file('sound')->store('sound/airole', 'public');
        }

        $aiRole->save();

        if ($request->has('language_ids') && $request->has('language_texts') && $request->has('question_texts')) {
            $languageIds = $request->input('language_ids', []);
            $languageTexts = $request->input('language_texts', []);
            $questionTexts = $request->input('question_texts', []);

            $aiRole->languages()->detach(); // Detach old relationships

            foreach ($languageIds as $index => $languageId) {
                if ($languageId) {
                    $aiRole->languages()->attach($languageId, [
                        'language_text' => $languageTexts[$index] ?? null,
                        'question_text' => $questionTexts[$index] ?? null,
                    ]);
                }
            }
        }

        return response()->json([
            'status_code' => 1,
            'data' => $aiRole,
            'message' => 'AI Role updated successfully.'
        ], 200);
    }
}
