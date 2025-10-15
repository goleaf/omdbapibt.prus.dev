<?php

use App\Console\Commands\ParseMoviesCommand;
use App\Console\Commands\ParsePeopleCommand;
use App\Console\Commands\ParseTvShowsCommand;
use App\Http\Middleware\EnsureUserIsSubscribed;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'subscribed' => EnsureUserIsSubscribed::class,
        ]);
    })
    ->withCommands([
        ParseMoviesCommand::class,
        ParseTvShowsCommand::class,
        ParsePeopleCommand::class,
    ])
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
