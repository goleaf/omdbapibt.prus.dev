<?php

use App\Console\Commands\CheckOmdbApi;
use App\Console\Commands\CleanupExpiredTrials;
use App\Console\Commands\Omdb\FetchOmdbKeysFromRemote;
use App\Console\Commands\Omdb\GenerateOmdbKeys;
use App\Console\Commands\Omdb\ParseMoviesWithApiKeys;
use App\Console\Commands\Parser\HydrateMovies;
use App\Console\Commands\Parser\HydratePeople;
use App\Console\Commands\Parser\HydrateTvShows;
use App\Console\Commands\RefreshRecommendationCache;
use App\Http\Middleware\EnsureSubscriptionAccess;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\ValidateLocale;
use App\Providers\AuthServiceProvider;
use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

if (empty($_ENV['APP_KEY'] ?? null) && empty($_SERVER['APP_KEY'] ?? null)) {
    $generatedKey = 'base64:'.base64_encode(random_bytes(32));

    $_ENV['APP_KEY'] = $generatedKey;
    $_SERVER['APP_KEY'] = $generatedKey;
    putenv('APP_KEY='.$generatedKey);
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withProviders([
        AuthServiceProvider::class,
    ])
    ->withCommands([
        CheckOmdbApi::class,
        GenerateOmdbKeys::class,
        FetchOmdbKeysFromRemote::class,
        ParseMoviesWithApiKeys::class,
        CleanupExpiredTrials::class,
        HydrateMovies::class,
        HydrateTvShows::class,
        HydratePeople::class,
        RefreshRecommendationCache::class,
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'subscriber' => EnsureSubscriptionAccess::class,
            'admin' => EnsureUserIsAdmin::class,
            'auth.basic' => AuthenticateWithBasicAuth::class,
            'validate-locale' => ValidateLocale::class,
            'set-locale' => SetLocale::class,
        ]);

        $middleware->prependToGroup('api', AuthenticateWithBasicAuth::class);
    })
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('trials:cleanup')->dailyAt('02:00');
        $schedule->command('recommendations:refresh')->dailyAt('03:30');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
