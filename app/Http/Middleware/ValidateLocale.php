<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class ValidateLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = Config::get('translatable.locales', []);
        $fallbackLocale = Config::get(
            'translatable.fallback_locale',
            Config::get('app.fallback_locale', 'en')
        );

        if ($supportedLocales === []) {
            $supportedLocales = [$fallbackLocale];
        } elseif (! in_array($fallbackLocale, $supportedLocales, true)) {
            $supportedLocales[] = $fallbackLocale;
        }

        $locale = $request->route('locale');

        if (! is_string($locale) || ! in_array($locale, $supportedLocales, true)) {
            abort(404);
        }

        URL::defaults(['locale' => $locale]);

        return $next($request);
    }
}
