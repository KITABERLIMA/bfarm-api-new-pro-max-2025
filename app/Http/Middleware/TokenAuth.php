<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class TokenAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Mendapatkan token dari header Authorization
        $token = $request->bearerToken();

        if (!$token) {
            // Jika token tidak tersedia, kembalikan respons unauthorized
            return $this->unauthorizedResponse('Token not provided.');
        }

        // Mencari token di database
        $tokenExists = PersonalAccessToken::findToken($token);

        if (!$tokenExists) {
            // Jika token tidak ditemukan, kembalikan respons unauthorized
            return $this->unauthorizedResponse('Token is invalid.');
        }

        // Jika token valid, lanjutkan request
        return $next($request);
    }

    private function unauthorizedResponse(): Response
    {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized. Token not provided or invalid.',
        ], 401);
    }
}
