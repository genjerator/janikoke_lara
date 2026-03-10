<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $googleUser = null;
        $sessionId  = $request->cookie('session_id');

        if ($sessionId) {
            try {
                $goServiceUrl = rtrim(env('GO_AUTH_SERVICE_URL', 'http://localhost:8080'), '/');
                $response = Http::withCookies(['session_id' => $sessionId], parse_url($goServiceUrl, PHP_URL_HOST))
                    ->timeout(2)
                    ->get($goServiceUrl . '/me');

                if ($response->successful()) {
                    $googleUser = $response->json();
                }
            } catch (\Throwable $e) {
                // Go service unreachable — treat as not logged in
            }
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user'        => $request->user(),
                'google_user' => $googleUser,
            ],
        ];
    }
}
