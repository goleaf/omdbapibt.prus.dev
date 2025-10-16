<?php

use App\Http\Controllers\Api\ImportOmdbKeysController;
use App\Http\Controllers\Api\MovieLookupController;
use App\Http\Controllers\Api\ParserTriggerController;
use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;
use Illuminate\Support\Facades\Route;

Route::get('movies/lookup', MovieLookupController::class)
    ->name('api.movies.lookup')
    ->withoutMiddleware([AuthenticateWithBasicAuth::class, 'auth.basic'])
    ->middleware('throttle:movie-lookup');

Route::post('parser/trigger', ParserTriggerController::class)
    ->name('api.parser.trigger')
    ->middleware('throttle:parser-trigger');

Route::post('omdb-keys/import', ImportOmdbKeysController::class)
    ->name('api.omdb-keys.import');
