<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $supportedLocales = Config::get('translatable.locales', []);
        $fallbackLocale = Config::get(
            'translatable.fallback_locale',
            Config::get('app.fallback_locale', 'en')
        );
        $sessionKey = 'app_locale';
        $locale = null;

        $pathLocale = $request->segment(1);
        if ($pathLocale && in_array($pathLocale, $supportedLocales, true)) {
            $locale = $pathLocale;

            session([$sessionKey => $locale]);

            $user = $request->user();
            if ($user && $user->preferred_locale !== $locale) {
                $user->forceFill(['preferred_locale' => $locale])->save();
            }
        }

        if (! $locale) {
            $user = $request->user();
            if (
                $user &&
                $user->preferred_locale &&
                in_array($user->preferred_locale, $supportedLocales, true)
            ) {
                $locale = $user->preferred_locale;
                session([$sessionKey => $locale]);
            }
        }

        if (! $locale) {
            $sessionLocale = session($sessionKey);
            if ($sessionLocale && in_array($sessionLocale, $supportedLocales, true)) {
                $locale = $sessionLocale;
            }
        }

        if (! $locale) {
            $preferred = $request->getPreferredLanguage($supportedLocales);
            if ($preferred && in_array($preferred, $supportedLocales, true)) {
                $locale = $preferred;
            }
        }

        if (! $locale || ! in_array($locale, $supportedLocales, true)) {
            $locale = $fallbackLocale;
        }

        App::setLocale($locale);
        Config::set('app.locale', $locale);
        Config::set('translatable.locale', $locale);

        if (! session()->has($sessionKey) || session($sessionKey) !== $locale) {
            session([$sessionKey => $locale]);
        }

        return $next($request);
    }
}
