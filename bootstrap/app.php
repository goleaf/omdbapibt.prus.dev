<?php

use App\Console\Commands\CleanupExpiredTrials;
use App\Console\Commands\ParseMoviesCommand;
use App\Console\Commands\ParsePeopleCommand;
use App\Console\Commands\ParseTvShowsCommand;
use App\Http\Middleware\EnsureSubscriptionAccess;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\SetLocale;
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
        ParseMoviesCommand::class,
        ParseTvShowsCommand::class,
        ParsePeopleCommand::class,
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
            'subscription.access' => EnsureSubscriptionAccess::class,
            'set-locale' => SetLocale::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('trials:cleanup')->dailyAt('02:00');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
