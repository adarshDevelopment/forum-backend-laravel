<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;


class SanctumGuestMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // if logged in send an error message as json response
        // can use request()->user? to check login status as well

        if (Auth::guard('sanctum')->check()) {
            return response()->json(['status' => false, 'message' => 'Cannot access this page wile the user is still logged in']);
        }
        return $next($request);
    }
}
