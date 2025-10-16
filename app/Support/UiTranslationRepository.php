<?php

namespace App\Support;

use App\Models\UiTranslation;
use Illuminate\Cache\CacheManager;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;

class UiTranslationRepository
{
    public function __construct(private CacheManager $cache) {}

    /**
     * @return array<string, array<string, string>>
     */
    public function all(): array
    {
        if (! Schema::hasTable('ui_translations')) {
            return [];
        }

        return $this->store()->rememberForever($this->cacheKey(), function (): array {
            $lines = [];

            UiTranslation::query()
                ->ordered()
                ->get()
                ->each(function (UiTranslation $translation) use (&$lines): void {
                    foreach ($translation->getTranslations('value') as $locale => $value) {
                        if ($value === '' || $value === null) {
                            continue;
                        }

                        $group = trim((string) $translation->group);
                        $key = trim((string) $translation->key);

                        if ($key === '') {
                            continue;
                        }

                        $composedKey = $group === '' ? $key : $group.'.'.$key;

                        $lines[$locale][$composedKey] = $value;
                    }
                });

            return $lines;
        });
    }

    public function forget(): void
    {
        $this->store()->forget($this->cacheKey());
    }

    public function refresh(): array
    {
        $this->forget();

        return $this->all();
    }

    public function register(): void
    {
        $this->registerLines($this->all());
    }

    public function refreshAndRegister(): void
    {
        $this->registerLines($this->refresh());
    }

    /**
     * @param  array<string, array<string, string>>  $lines
     */
    protected function registerLines(array $lines): void
    {
        foreach ($lines as $locale => $entries) {
            if ($entries === []) {
                continue;
            }

            $prepared = [];

            foreach ($entries as $key => $value) {
                $normalizedKey = trim((string) $key);

                if ($normalizedKey === '') {
                    continue;
                }

                $prepared['ui.'.$normalizedKey] = $value;
            }

            Lang::addLines($prepared, $locale);
        }
    }

    protected function store(): CacheRepository
    {
        $store = (string) config('ui-translations.cache.store', 'redis');

        try {
            return $this->cache->store($store);
        } catch (InvalidArgumentException) {
            $fallback = (string) config('ui-translations.cache.fallback_store', config('cache.default'));

            return $this->cache->store($fallback);
        }
    }

    protected function cacheKey(): string
    {
        return (string) config('ui-translations.cache.key', 'ui_translations.lines');
    }
}
