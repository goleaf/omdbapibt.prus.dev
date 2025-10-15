<?php

use App\Console\Commands\CleanupExpiredTrials;
use App\Http\Middleware\EnsureUserHasSubscription;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\ValidateLocale;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
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
            'validate-locale' => ValidateLocale::class,
            'set-locale' => SetLocale::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('trials:cleanup')->dailyAt('02:00');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
