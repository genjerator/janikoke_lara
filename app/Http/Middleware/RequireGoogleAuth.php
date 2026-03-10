<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireGoogleAuth
{
    /**
     * Handle an incoming request.
     * Only allows access if the user is authenticated AND logged in via Google
     * (i.e. the session has a google_session_id cookie set by the Go auth service,
     * OR the authenticated Laravel user has a google_id on their record).
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Option A: user is authenticated in Laravel and has a google_id on their account
        if ($request->user() && !empty($request->user()->google_id)) {
            return $next($request);
        }

        // Option B: no Laravel session but a google_session_id cookie is present
        // (set by the Go auth service after successful Google OAuth)
        if ($request->cookie('session_id')) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json(['error' => 'Google authentication required.'], 403);
        }

        return redirect()->route('home')->with('error', 'You must be logged in with Google to access this page.');
    }
}

