<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthorization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            if (Auth::user()->role_id == 2 || 3) {
                return $next($request);
            } else {
                return response()->json(['error' => 'Unauthorized access, admin only.'], 403);
            }
        }

        return response()->json(['error' => 'Authentication required.'], 401);
    }
}
