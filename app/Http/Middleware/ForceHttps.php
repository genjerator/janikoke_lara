<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceHttps
{
    public function handle(Request $request, Closure $next): Response
    {
        $shouldForce = app()->environment('production') || (bool) env('FORCE_HTTPS', false);

        if (!$shouldForce || $request->isSecure()) {
            return $next($request);
        }

        return redirect()->secure($request->getRequestUri(), 301);
    }
}

