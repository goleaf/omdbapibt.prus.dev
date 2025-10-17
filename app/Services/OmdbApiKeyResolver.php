<?php

namespace App\Services;

use App\Models\OmdbApiKey;
use Illuminate\Cache\Repository as CacheRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
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
            if (! $this->tableExists()) {
                return $this->fallback();
            }

            $keys = $this->validKeyQuery()->pluck('key');

            if ($keys->isEmpty()) {
                return $this->fallback();
            }

            $key = $this->selectNextKey($keys);

            $this->rememberLastUsed($key);
        } catch (Throwable $exception) {
            return $this->fallback();
        }

        return $key ?: $this->fallback();
    }

    public function __invoke(): string
    {
        return $this->resolve();
    }

    public function reportSuccess(string $key, ?int $statusCode = null): void
    {
        $this->whenTableExists(function () use ($key, $statusCode): void {
            /** @var OmdbApiKey|null $record */
            $record = OmdbApiKey::query()->where('key', $key)->first();

            if (! $record) {
                return;
            }

            $now = Carbon::now();

            $record->status = OmdbApiKey::STATUS_VALID;
            $record->last_checked_at = $now;
            $record->last_confirmed_at = $now;
            $record->last_response_code = $statusCode;
            $record->consecutive_failures = 0;
            $record->disabled_until = null;

            $record->save();
        });
    }

    public function reportFailure(string $key, ?int $statusCode = null, ?string $reason = null): void
    {
        $this->whenTableExists(function () use ($key, $statusCode): void {
            /** @var OmdbApiKey|null $record */
            $record = OmdbApiKey::query()->where('key', $key)->first();

            if (! $record) {
                return;
            }

            $now = Carbon::now();

            $record->last_checked_at = $now;
            $record->last_response_code = $statusCode;
            $record->consecutive_failures = min((int) $record->consecutive_failures + 1, 65_535);

            $threshold = max(1, (int) config('services.omdb.validation.failure_threshold', 3));
            $backoffMinutes = max(1, (int) config('services.omdb.validation.failure_backoff_minutes', 15));

            $shouldDisable = $record->consecutive_failures >= $threshold;

            if ($shouldDisable) {
                $record->disabled_until = $now->copy()->addMinutes($backoffMinutes);
                $record->status = OmdbApiKey::STATUS_UNKNOWN;
            }

            $record->save();

            if ($shouldDisable) {
                $this->forgetLastUsedIfMatches($key);
            }
        });
    }

    public function resetKey(string $key, bool $markPending = false): void
    {
        $this->whenTableExists(function () use ($key, $markPending): void {
            /** @var OmdbApiKey|null $record */
            $record = OmdbApiKey::query()->where('key', $key)->first();

            if (! $record) {
                return;
            }

            $record->consecutive_failures = 0;
            $record->disabled_until = null;

            if ($markPending) {
                $record->status = OmdbApiKey::STATUS_PENDING;
            }

            $record->save();

            $this->forgetLastUsedIfMatches($key);
        });
    }

    public function health(?string $key = null): array
    {
        if (! $this->tableExists()) {
            return [];
        }

        $query = OmdbApiKey::query();

        if ($key !== null) {
            $query->where('key', $key);
        }

        return $query
            ->orderBy('key')
            ->get()
            ->map(function (OmdbApiKey $record): array {
                return [
                    'key' => $record->key,
                    'status' => $record->status,
                    'consecutive_failures' => (int) $record->consecutive_failures,
                    'disabled_until' => $record->disabled_until,
                    'last_response_code' => $record->last_response_code,
                    'last_checked_at' => $record->last_checked_at,
                    'last_confirmed_at' => $record->last_confirmed_at,
                ];
            })
            ->keyBy('key')
            ->toArray();
    }

    protected function fallback(): string
    {
        return $this->fallbackKey ?? '';
    }

    protected function cacheKey(): string
    {
        return 'services:omdb:resolver:last_used';
    }

    protected function selectNextKey(Collection $keys): string
    {
        $keys = $keys->values();
        $lastUsed = $this->lastUsedKey();

        if ($lastUsed === null) {
            return (string) $keys->first();
        }

        $index = $keys->search($lastUsed, strict: true);

        if ($index === false) {
            return (string) $keys->first();
        }

        $nextIndex = ($index + 1) % $keys->count();

        return (string) $keys[$nextIndex];
    }

    protected function rememberLastUsed(string $key): void
    {
        $this->cache->forever($this->cacheKey(), $key);
    }

    protected function lastUsedKey(): ?string
    {
        $lastUsed = $this->cache->get($this->cacheKey());

        return is_string($lastUsed) && $lastUsed !== '' ? $lastUsed : null;
    }

    protected function forgetLastUsedIfMatches(string $key): void
    {
        if ($this->lastUsedKey() === $key) {
            $this->cache->forget($this->cacheKey());
        }
    }

    protected function tableExists(): bool
    {
        try {
            return \Schema::hasTable('omdb_api_keys');
        } catch (Throwable) {
            return false;
        }
    }

    protected function whenTableExists(callable $callback): void
    {
        if (! $this->tableExists()) {
            return;
        }

        try {
            $callback();
        } catch (Throwable) {
            // Swallow exceptions when updating health state to avoid
            // interrupting API requests with bookkeeping failures.
        }
    }

    protected function validKeyQuery(): Builder
    {
        $thresholdMinutes = (int) config('services.omdb.validation.health_grace_minutes', 0);

        $query = OmdbApiKey::query()
            ->where('status', OmdbApiKey::STATUS_VALID)
            ->where(function (Builder $builder): void {
                $builder->whereNull('disabled_until')
                    ->orWhere('disabled_until', '<=', Carbon::now());
            });

        if ($thresholdMinutes > 0) {
            $query->where(function (Builder $builder) use ($thresholdMinutes): void {
                $builder->whereNull('last_confirmed_at')
                    ->orWhere('last_confirmed_at', '>=', Carbon::now()->subMinutes($thresholdMinutes));
            });
        }

        return $query
            ->orderByDesc('last_confirmed_at')
            ->orderByDesc('updated_at')
            ->orderByDesc('id');
    }
}
