<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminAuthorization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            // Periksa jika role_id sama dengan 3
            if (Auth::user()->role_id == 3) {
                return $next($request);
            } else {
                return response()->json(['error' => 'Unauthorized access, super admin only.'], 403);
            }
        }

        return response()->json(['error' => 'Authentication required.'], 401);
    }
}
