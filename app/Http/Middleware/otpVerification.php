<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class otpVerification
{
  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle(Request $request, Closure $next): Response
  {
    if (Auth::check()) {
      // Periksa jika akun sudah aktif
      if (Auth::user()->activation == 'active') {
        return $next($request);
      } else {
        return response()->json(['error' => 'Please verify OTP code.'], 403);
      }
    }

    return response()->json(['error' => 'Authentication required.'], 401);
  }
}
