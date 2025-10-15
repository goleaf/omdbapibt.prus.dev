<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/status', function () {
    return response()->json([
        'status' => 'ok',
    ]);
})->name('api.status');

Route::middleware(['auth', 'throttle:parser-triggers'])
    ->withoutMiddleware(['throttle:public-api'])
    ->post('/parsers/run', function (Request $request) {
        return response()->json([
            'message' => 'Parser trigger accepted.',
        ], 202);
    })->name('api.parsers.run');
