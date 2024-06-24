<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\Land;

class Authorization
{
  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle(Request $request, Closure $next): Response
  {
    try {
      // 1. Retrieve the authenticated user
      $user = Auth::guard('sanctum')->user();

      if (!$user) {
        throw new HttpResponseException(response()->json([
          'error' => 'Unauthenticated'
        ], 401));
      }

      // 2. Get the land ID from the request parameters
      $landId = $request->route('id');

      if (!$landId) {
        throw new HttpResponseException(response()->json([
          'error' => 'Land ID not provided'
        ], 400));
      }

      // 3. Query the 'lands' table to find the record with the given land ID
      $land = Land::find($landId);

      if (!$land) {
        throw new HttpResponseException(response()->json([
          'error' => 'Land not found'
        ], 404));
      }

      // 4. Compare the user ID from the authenticated user with the user ID from the land record
      if ($land->user_id !== $user->id) {
        throw new HttpResponseException(response()->json([
          'error' => 'Unauthorized'
        ], 403));
      }
    } catch (\Exception $e) {
      return response()->json(['error' => $e->getMessage()], $e->getCode() ?: 400);
    }

    // If all checks pass, proceed with the request
    return $next($request);
  }
}
