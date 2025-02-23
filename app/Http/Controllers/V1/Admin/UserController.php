<?php

namespace App\Http\Controllers\V1\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10); // Default to 10 items per page
        $search = $request->query('search');
        $aiRoleId = $request->query('ai_role_id');
        $languageId = $request->query('language_id');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date') ?? Carbon::today()->toDateString(); 

        $query = User::with(['preferredAiRole', 'preferredLanguage'])
            ->orderBy('created_at', 'desc');

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                  ->orWhere('mobile_number', 'like', '%' . $search . '%');
        }

        if ($aiRoleId) {
            $query->where('preferred_ai_role_id', $aiRoleId);
        }

        if ($languageId) {
            $query->where('preferred_language_id', $languageId);
        }

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        $query->where('role','!=','admin');
        $query->where('name','!=',null);
        $users = $query->paginate($perPage);

        return response()->json([
            'status_code' => 1,
            'data' => $users->items(),
            'pagination' => [
                'total' => $users->total(),
                'per_page' => $users->perPage(),
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'from' => $users->firstItem(),
                'to' => $users->lastItem(),
            ],
            'message' => 'Users retrieved successfully.'
        ], 200);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if(!$user) {
            return response()->json([
                'status_code' => 2,
                'message' => 'User not found.'
            ], 404);
        }
        $user->delete();

        return response()->json([
            'status_code' => 1,
            'message' => 'User deleted successfully.'
        ], 200);
    }
    public function getTotalUsers()
    {
        $totalUsers = User::where('role','!=','admin')->count();

        $disabledUsers = User::where('role', '!=', 'admin')
                    ->where('status', 'inactive')
                    ->count();

        return response()->json([
            'status_code' => 1,
            'data' => [
                'total_users' => $totalUsers,
                'disabled_user' => $disabledUsers
            ],
            'message' => 'Total number of users retrieved successfully.'
        ], 200);
    }
    public function update(Request $request,$id)
    {
        $request->validate([
            'status' => 'required|in:active,inactive',
        ]);
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status_code' => 2,
                'message' => 'User not found.'
            ], 404);
        }

        // Update the user's status
        $user->status = $request->status;
        $user->save();

        return response()->json([
            'status_code' => 1,
            'data' => $user,
            'message' => 'User status updated successfully.'
        ], 200);
    }
}
