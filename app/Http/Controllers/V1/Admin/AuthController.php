<?php

namespace App\Http\Controllers\V1\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Fetch the user with the given email and role 'admin'
        $user = User::where('email', $request->email)->where('role', 'admin')->first();

        // Check if user exists
        if (!$user) {
            return response()->json([
                'status_code' => 2,
                'data' => [],
                'message' => 'Account not registered'
            ], 404);
        }

        // Check if the user's account is active
        if ($user->status != 'active') {
            return response()->json([
                'status_code' => 2,
                'data' => [],
                'message' => 'Account not active'
            ], 403);
        }

        // Verify the password
        if (!Hash::check($request->input('password'), $user->password)) {
            return response()->json([
                'status_code' => 2,
                'data' => [],
                'message' => 'Incorrect password'
            ], 401);
        }

        // Generate an API token for the user
        $token = $user->createToken('api-token')->plainTextToken;

        // Return the success response with the user and token data
        return response()->json([
            'status_code' => 1,
            'data' => [
                'user' => $user,
                'token' => $token
            ],
            'message' => 'Login successful.'
        ]);
    }
}
