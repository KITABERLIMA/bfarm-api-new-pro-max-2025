<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;

class Authorization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $currentUser = Auth::user();
        $userdata = User::findOrFail($request->email);

        if ($userdata->email != $currentUser->email) {
            throw new HttpResponseException(response([
                "error" => [
                    "Unauthorized"
                ]
            ], 402));
        }

        return $next($request);
    }
}