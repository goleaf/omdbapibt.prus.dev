<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class EnsureLocaleIsSupported
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $locale = (string) $request->route('locale');

        $supportedLocales = Config::get('app.supported_locales', [Config::get('app.locale')]);

        if (! in_array($locale, $supportedLocales, true)) {
            abort(404);
        }

        App::setLocale($locale);
        URL::defaults(['locale' => $locale]);

        return $next($request);
    }
}
