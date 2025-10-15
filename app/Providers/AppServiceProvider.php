<?php

namespace App\Providers;

use App\Support\ImpersonationManager;
use App\Support\RedisStubStore;
use App\Support\UiTranslationRepository;
use Illuminate\Cache\CacheManager;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\App\Services\Clients\TmdbClient::class, function ($app) {
            return new \App\Services\Clients\TmdbClient(
                $app->make(\Illuminate\Http\Client\Factory::class),
                $app->make(\Illuminate\Cache\CacheManager::class),
                (string) config('services.tmdb.key', ''),
                (string) config('services.tmdb.base_url', 'https://api.themoviedb.org/3/')
            );
        });

        $this->app->singleton(\App\Services\Clients\OmdbClient::class, function ($app) {
            return new \App\Services\Clients\OmdbClient(
                $app->make(\Illuminate\Http\Client\Factory::class),
                $app->make(\Illuminate\Cache\CacheManager::class),
                (string) config('services.omdb.key', ''),
                (string) config('services.omdb.base_url', 'https://www.omdbapi.com/')
            );
        });

        $this->app->singleton(UiTranslationRepository::class, function ($app) {
            return new UiTranslationRepository($app->make(CacheManager::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // SQLite-specific tuning and safety toggles
        try {
            if (config('database.default') === 'sqlite') {
                // Core safety and concurrency settings
                DB::statement('PRAGMA foreign_keys = ON');
                DB::statement('PRAGMA journal_mode = WAL');
                DB::statement('PRAGMA synchronous = NORMAL');
                DB::statement('PRAGMA temp_store = MEMORY');
                DB::statement('PRAGMA cache_size = -20000');
                DB::statement('PRAGMA busy_timeout = 5000');

                // Optional: allow tuning via env, safe to ignore if unsupported
                $mmap = (int) env('SQLITE_MMAP_SIZE', 0);
                if ($mmap > 0) {
                    DB::statement('PRAGMA mmap_size = '.$mmap);
                }

                $walCheckpoint = (int) env('SQLITE_WAL_AUTOCHECKPOINT', 1000);
                if ($walCheckpoint >= 0) {
                    DB::statement('PRAGMA wal_autocheckpoint = '.$walCheckpoint);
                }
            }
        } catch (\Throwable $e) {
            // Never break boot on shared hosts; log and continue
            Log::warning('SQLite PRAGMA setup skipped: '.$e->getMessage());
        }

        // Keep DB query log off in production to avoid memory overhead
        if (app()->isProduction()) {
            DB::disableQueryLog();
        }

        $this->configureRateLimiting();
        $this->registerRedisStubDriver();
        $this->loadUiTranslations();

        if ($this->app->environment('testing')) {
            Vite::useBuildDirectory('../tests/fixtures/vite');
        }

        $this->shareImpersonationContext();
    }

    protected function registerRedisStubDriver(): void
    {
        Cache::extend('redis_stub', function ($app) {
            return Cache::repository(new RedisStubStore);
        });
    }

    protected function loadUiTranslations(): void
    {
        try {
            if (! Schema::hasTable('ui_translations')) {
                return;
            }
        } catch (\Throwable $exception) {
            if (! app()->runningInConsole()) {
                report($exception);
            }

            return;
        }

        try {
            $this->app->make(UiTranslationRepository::class)->register();
        } catch (\Throwable $exception) {
            if (! app()->runningInConsole()) {
                report($exception);
            }
        }
    }

    protected function shareImpersonationContext(): void
    {
        View::composer(['layouts.app', 'layouts.dashboard'], function ($view): void {
            /** @var ImpersonationManager $impersonationManager */
            $impersonationManager = app(ImpersonationManager::class);

            if (! $impersonationManager->isImpersonating()) {
                return;
            }

            $impersonator = $impersonationManager->impersonator();
            $actingAs = auth()->user();

            if (! $impersonator || ! $actingAs) {
                return;
            }

            $view->with('impersonationBannerContext', [
                'impersonator' => $impersonator,
                'actingAs' => $actingAs,
            ]);
        });
    }

    protected function configureRateLimiting(): void
    {
        RateLimiter::for('movie-lookup', function (Request $request): Limit {
            return $this->buildRateLimit('movie_lookup')
                ->by((string) $request->ip());
        });

        RateLimiter::for('parser-trigger', function (Request $request): Limit {
            return $this->buildRateLimit('parser_trigger')
                ->by((string) ($request->user()?->getAuthIdentifier() ?? $request->ip()));
        });
    }

    protected function buildRateLimit(string $key): Limit
    {
        $config = (array) config("rate-limiting.{$key}");

        $maxAttempts = max(1, (int) ($config['max_attempts'] ?? 60));
        $decaySeconds = max(1, (int) ($config['decay_seconds'] ?? 60));

        return Limit::perSecond($maxAttempts, $decaySeconds);
    }
}
