<?php

namespace Tests\Feature\Localization;

use App\Support\TranslationRepository;
use Illuminate\Support\Str;
use Tests\TestCase;

class TranslationParityTest extends TestCase
{
    public function test_all_locales_match_the_default_locale_keys(): void
    {
        $repository = app(TranslationRepository::class);
        $defaultLocale = config('app.fallback_locale', config('app.locale'));
        $defaultKeys = $repository->keysFor($defaultLocale);

        foreach ($repository->locales() as $locale) {
            if ($locale === $defaultLocale) {
                continue;
            }

            $missing = $repository->missingKeys($defaultLocale, $locale);
            $extra = $repository->extraKeys($defaultLocale, $locale);

            $this->assertEmpty(
                $missing,
                sprintf('Locale [%s] is missing translation keys: %s', $locale, $this->previewKeys($missing))
            );

            $this->assertEmpty(
                $extra,
                sprintf('Locale [%s] contains unexpected translation keys: %s', $locale, $this->previewKeys($extra))
            );

            $this->assertSameSize(
                $defaultKeys,
                $repository->keysFor($locale),
                sprintf('Locale [%s] should have the same number of translation entries as [%s].', $locale, $defaultLocale)
            );
        }
    }

    public function test_all_translation_values_are_strings(): void
    {
        $repository = app(TranslationRepository::class);

        foreach ($repository->locales() as $locale) {
            $entries = $repository->entriesFor($locale);

            foreach ($entries as $key => $value) {
                $this->assertIsString(
                    $value,
                    sprintf('Translation [%s] in locale [%s] must resolve to a string.', $key, $locale)
                );
            }
        }
    }

    /**
     * @param  array<int, string>  $keys
     */
    private function previewKeys(array $keys): string
    {
        $preview = array_slice($keys, 0, 5);

        return Str::of(implode(', ', $preview))
            ->when(count($keys) > 5, static fn ($string) => $string.' …')
            ->toString();
    }
}
