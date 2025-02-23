<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Assuming 'role' field exists in your user model and 1 represents admin
        if (Auth::check() && Auth::user()->role == 'admin') {
            return $next($request);
        }

        return response()->json(['status_code' => 403, 'message' => 'Access denied.'], 403);
    }
}
