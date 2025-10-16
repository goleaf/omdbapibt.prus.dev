<?php

namespace App\Services;

use App\Models\OmdbApiKey;
use Illuminate\Cache\Repository as CacheRepository;
use Illuminate\Support\Carbon;
use Throwable;

class OmdbApiKeyResolver
{
    public function __construct(
        protected CacheRepository $cache,
        protected ?string $fallbackKey = null
    ) {
        $this->fallbackKey ??= (string) config('services.omdb.key', '');
    }

    public function resolve(): string
    {
        try {
            if (! \Schema::hasTable('omdb_api_keys')) {
                return $this->fallback();
            }

            $thresholdMinutes = (int) config('services.omdb.validation.health_grace_minutes', 0);

            $query = OmdbApiKey::query()
                ->where('status', OmdbApiKey::STATUS_VALID);

            if ($thresholdMinutes > 0) {
                $query->where(function ($builder) use ($thresholdMinutes): void {
                    $builder->whereNull('last_confirmed_at')
                        ->orWhere('last_confirmed_at', '>=', Carbon::now()->subMinutes($thresholdMinutes));
                });
            }

            $keys = $query
                ->orderByDesc('last_confirmed_at')
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->pluck('key');

            if ($keys->isEmpty()) {
                return $this->fallback();
            }

            $lastUsed = (string) $this->cache->get($this->cacheKey());
            $index = $keys->search($lastUsed, strict: true);

            if ($index !== false) {
                $nextIndex = ($index + 1) % $keys->count();
                $key = (string) $keys[$nextIndex];
            } else {
                $key = (string) $keys->first();
            }

            $this->cache->put($this->cacheKey(), $key, Carbon::now()->addMinutes(30));
        } catch (Throwable $exception) {
            return $this->fallback();
        }

        return $key ?: $this->fallback();
    }

    public function __invoke(): string
    {
        return $this->resolve();
    }

    protected function fallback(): string
    {
        return $this->fallbackKey ?? '';
    }

    protected function cacheKey(): string
    {
        return 'services:omdb:resolver:last_used';
    }
}
