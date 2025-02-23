<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuOption;
use Illuminate\Http\Request;

class MenuOptionController extends Controller
{
    public function index()
    {
        $menuOptions = MenuOption::orderBy('rank', 'asc')->get();
        return response()->json([
            'status_code' => 1,
            'data' => $menuOptions,
            'message' => 'Menu options retrieved successfully.'
        ], 200);
    }

    // Create a new menu option
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'nullable|string',
            'rank' => 'nullable|integer',
            'icon' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $menuOption = new MenuOption();
        $menuOption->name = $request->name;
        $menuOption->url = $request->url;
        $menuOption->rank = $request->rank;
        $menuOption->icon = $request->file('icon')->store('images/menu', 'public');
        $menuOption->save();

        return response()->json([
            'status_code' => 1,
            'data' => $menuOption,
            'message' => 'Menu option created successfully.'
        ], 201);
    }

    // Show a specific menu option
    public function show($id)
    {
        $menuOption = MenuOption::find($id);

        if (!$menuOption) {
            return response()->json([
                'status_code' => 2,
                'message' => 'Menu option not found.'
            ], 404);
        }

        return response()->json([
            'status_code' => 1,
            'data' => $menuOption,
            'message' => 'Menu option retrieved successfully.'
        ], 200);
    }

    // Update a menu option
    public function update(Request $request, $id)
    {
        // Validate the incoming request data
        $request->validate([
            'name' => 'nullable|string|max:255',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'url' => 'nullable|string|max:255',
            'rank' => 'nullable|integer',
        ]);
    
        // Find the menu option by ID
        $menuOption = MenuOption::find($id);
        if (!$menuOption) {
            return response()->json([
                'status_code' => 2,
                'message' => 'Menu option not found.',
            ], 404);
        }
    
        // Conditionally update fields if they exist in the request
        if ($request->has('name')) {
            $menuOption->name = $request->name;
        }
    
        if ($request->hasFile('icon')) {
            $menuOption->icon = $request->file('icon')->store('images/icon', 'public');
        }
    
        if ($request->has('url')) {
            $menuOption->url = $request->url;
        }
    
        if ($request->has('rank')) {
            $menuOption->rank = $request->rank;
        }
    
        // Save the updated menu option
        $menuOption->save();
    
        return response()->json([
            'status_code' => 1,
            'data' => $menuOption,
            'message' => 'Menu option updated successfully.',
        ], 200);
    }
    

    // Delete a menu option
    public function destroy($id)
    {
        $menuOption = MenuOption::find($id);

        if (!$menuOption) {
            return response()->json([
                'status_code' => 2,
                'message' => 'Menu option not found.'
            ], 404);
        }

        $menuOption->delete();

        return response()->json([
            'status_code' => 1,
            'message' => 'Menu option deleted successfully.'
        ], 200);
    }
}
