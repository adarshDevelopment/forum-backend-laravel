<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SanctumCustomMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$params): Response
    {
        // return response()->json(['message' => $params], 403);


        // if (!request()->user()) {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json(['message' => 'User not logged in'], 403);
        }
        return $next($request);
    }
}
