<?php

namespace App\Support;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use RuntimeException;
use SplFileInfo;

class TranslationRepository
{
    public function __construct(private readonly Filesystem $files) {}

    /**
     * @return array<int, string>
     */
    public function locales(): array
    {
        return collect($this->files->directories(lang_path()))
            ->map(static fn (string $directory): string => basename($directory))
            ->sort()
            ->values()
            ->all();
    }

    /**
     * @return array<int, string>
     */
    public function keysFor(string $locale): array
    {
        return array_keys($this->entriesFor($locale));
    }

    /**
     * @return array<string, string>
     */
    public function entriesFor(string $locale): array
    {
        $localePath = lang_path($locale);

        if (! $this->files->exists($localePath)) {
            throw new FileNotFoundException("Locale directory [{$locale}] was not found.");
        }

        return collect($this->files->allFiles($localePath))
            ->filter(static fn (SplFileInfo $file): bool => $file->getExtension() === 'php')
            ->sortBy(static fn (SplFileInfo $file): string => $file->getPathname())
            ->flatMap(function (SplFileInfo $file) use ($localePath): array {
                $relativePath = trim(str_replace($localePath, '', $file->getPathname()), DIRECTORY_SEPARATOR);
                $group = str_replace(['.php', DIRECTORY_SEPARATOR], ['', '.'], $relativePath);

                $translations = require $file->getPathname();

                if (! is_array($translations)) {
                    throw new RuntimeException(sprintf('Translation file [%s] must return an array.', $file->getPathname()));
                }

                return collect(Arr::dot($translations))
                    ->mapWithKeys(static function ($value, string $key) use ($group): array {
                        $translationKey = $group !== '' ? $group.'.'.$key : $key;

                        return [$translationKey => $value];
                    })
                    ->all();
            })
            ->all();
    }

    /**
     * @return array<int, string>
     */
    public function missingKeys(string $referenceLocale, string $locale): array
    {
        $referenceKeys = $this->keysFor($referenceLocale);
        $localeKeys = $this->keysFor($locale);

        return array_values(array_diff($referenceKeys, $localeKeys));
    }

    /**
     * @return array<int, string>
     */
    public function extraKeys(string $referenceLocale, string $locale): array
    {
        $referenceKeys = $this->keysFor($referenceLocale);
        $localeKeys = $this->keysFor($locale);

        return array_values(array_diff($localeKeys, $referenceKeys));
    }
}
