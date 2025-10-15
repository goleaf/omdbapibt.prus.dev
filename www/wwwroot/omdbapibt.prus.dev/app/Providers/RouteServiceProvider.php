<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('public-api', function (Request $request) {
            return Limit::perMinute(60)
                ->by($request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'message' => 'Too many requests. Please slow down.',
                    ], 429, $headers);
                });
        });

        RateLimiter::for('parser-triggers', function (Request $request) {
            $identifier = (string) ($request->user()?->getAuthIdentifier() ?? $request->ip());

            return Limit::perMinute(5)
                ->by($identifier)
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'message' => 'Too many parsing requests. Please wait before retrying.',
                    ], 429, $headers);
                });
        });
    }
}
