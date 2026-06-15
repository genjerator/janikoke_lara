<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Locales the app serves content in. Matches the janikoke54 mobile app:
     * en (English), sr (Serbian), rsn (Rusyn).
     */
    private const SUPPORTED = ['en', 'sr', 'rsn'];

    /**
     * Resolve the request locale from the `?lang=` query param or the
     * `Accept-Language` header, falling back to the app default.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->normalize(
            $request->query('lang') ?? $request->getPreferredLanguage(self::SUPPORTED)
        );

        if (in_array($locale, self::SUPPORTED, true)) {
            App::setLocale($locale);
        }

        return $next($request);
    }

    /**
     * Match the requested locale against the supported list, falling back to
     * the primary subtag (e.g. "sr-Latn" -> "sr"). Note codes vary in length
     * ("rsn"), so we never blindly truncate.
     */
    private function normalize(?string $locale): ?string
    {
        if (!is_string($locale)) {
            return null;
        }

        $locale = strtolower($locale);

        if (in_array($locale, self::SUPPORTED, true)) {
            return $locale;
        }

        $primary = explode('-', $locale)[0];

        return in_array($primary, self::SUPPORTED, true) ? $primary : $locale;
    }
}
