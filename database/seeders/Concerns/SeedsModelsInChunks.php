<?php

namespace Database\Seeders\Concerns;

use Closure;
use Illuminate\Support\Collection;

use function config;
use function fake;

trait SeedsModelsInChunks
{
    protected function seedInChunks(int $total, int $chunkSize, Closure $callback): void
    {
        $remaining = $total;

        while ($remaining > 0) {
            $batchSize = min($chunkSize, $remaining);

            $callback($batchSize);

            $remaining -= $batchSize;
        }
    }

    protected function chunkedInsert(Collection $payloads, int $chunkSize, Closure $inserter): void
    {
        $payloads
            ->chunk($chunkSize)
            ->each(static function (Collection $chunk) use ($inserter): void {
                $inserter($chunk->values()->all());
            });
    }

    /**
     * @return list<string>
     */
    protected function supportedLocales(): array
    {
        $locales = config('translatable.locales', ['en']);

        return is_array($locales) ? array_values($locales) : ['en'];
    }

    protected function fallbackLocale(): string
    {
        $fallback = config('translatable.fallback_locale', 'en');

        return is_string($fallback) ? $fallback : 'en';
    }

    protected function translationFallback(mixed $value): ?string
    {
        if (is_array($value)) {
            $fallbackLocale = $this->fallbackLocale();

            if (isset($value[$fallbackLocale]) && is_string($value[$fallbackLocale])) {
                return $value[$fallbackLocale];
            }

            $first = reset($value);

            return is_string($first) ? $first : null;
        }

        return $value !== null ? (string) $value : null;
    }

    /**
     * Normalize translation arrays ensuring that every supported locale has a value.
     *
     * @param  array<string, string>|null  $translations
     * @return array<string, string>
     */
    protected function fillTranslations(?array $translations, ?string $fallbackValue, Closure $generator): array
    {
        $normalized = is_array($translations) ? $translations : [];
        $fallbackLocale = $this->fallbackLocale();

        if ($fallbackValue !== null) {
            $normalized[$fallbackLocale] ??= $fallbackValue;
        }

        foreach ($this->supportedLocales() as $locale) {
            if (! array_key_exists($locale, $normalized) || ! is_string($normalized[$locale]) || $normalized[$locale] === '') {
                $normalized[$locale] = $generator($locale, $fallbackValue);
            }
        }

        return $normalized;
    }

    protected function localizedSentence(string $locale): string
    {
        return fake($this->fakerLocale($locale))->sentence(3);
    }

    protected function localizedParagraph(string $locale): string
    {
        return fake($this->fakerLocale($locale))->paragraph();
    }

    protected function fakerLocale(string $locale): string
    {
        return match ($locale) {
            'es' => 'es_ES',
            'fr' => 'fr_FR',
            default => 'en_US',
        };
    }
}
