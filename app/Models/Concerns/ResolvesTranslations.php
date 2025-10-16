<?php

namespace App\Models\Concerns;

use Illuminate\Support\Arr;

trait ResolvesTranslations
{
    protected function resolveLocalizedValue(?array $translations, ?string $fallback, ?string $locale = null): string
    {
        $translations = array_filter($translations ?? [], function ($value): bool {
            return is_string($value) && $value !== '';
        });

        $locale = $locale ?: app()->getLocale();

        if ($locale && Arr::has($translations, $locale)) {
            return (string) $translations[$locale];
        }

        $fallbackLocale = config('app.fallback_locale');

        if ($fallbackLocale && Arr::has($translations, $fallbackLocale)) {
            return (string) $translations[$fallbackLocale];
        }

        if (Arr::has($translations, 'en')) {
            return (string) $translations['en'];
        }

        if ($translations !== []) {
            return (string) reset($translations);
        }

        if ($fallback) {
            return $fallback;
        }

        return '';
    }
}
