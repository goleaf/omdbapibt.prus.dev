<?php

use Illuminate\Support\Arr;

if (! function_exists('localized_route')) {
    /**
     * Generate the URL to a named route including the active locale parameter.
     */
    function localized_route(string $name, array $parameters = [], bool $absolute = true, ?string $locale = null): string
    {
        $localeParameter = $parameters['locale'] ?? $locale ?? app()->getLocale() ?? config('app.fallback_locale');

        $parameters = ['locale' => $localeParameter] + Arr::except($parameters, ['locale']);

        return route($name, $parameters, $absolute);
    }
}
