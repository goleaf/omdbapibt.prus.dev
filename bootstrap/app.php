<?php

use App\Console\Commands\CleanupExpiredTrials;
use App\Http\Middleware\EnsureUserHasSubscription;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\ValidateLocale;
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
    ->withCommands([
        CleanupExpiredTrials::class,
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'subscriber' => EnsureUserHasSubscription::class,
            'admin' => EnsureUserIsAdmin::class,
            'auth.basic' => AuthenticateWithBasicAuth::class,
            'validate-locale' => ValidateLocale::class,
            'set-locale' => SetLocale::class,
        ]);

        $middleware->prependToGroup('api', AuthenticateWithBasicAuth::class);
    })
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('trials:cleanup')->dailyAt('02:00');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
