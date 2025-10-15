<?php

use App\Http\Controllers\Api\MovieShowController;
use App\Http\Controllers\Api\ParserTriggerController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->as('api.v1.')->group(function (): void {
    Route::middleware('throttle:public-api')->group(function (): void {
        Route::get('movies/{movie:slug}', MovieShowController::class)
            ->name('movies.show');
    });

    Route::middleware(['auth.basic', 'admin', 'throttle:parser-trigger'])->group(function (): void {
        Route::post('parser/dispatch', ParserTriggerController::class)
            ->name('parser.dispatch');
    });
});
