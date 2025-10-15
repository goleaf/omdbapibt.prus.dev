<?php

namespace App\Providers;

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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
