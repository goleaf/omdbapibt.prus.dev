<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsSubscribed
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $name = 'default'): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        if ($user->subscribed($name) || $user->onTrial()) {
            return $next($request);
        }

        abort(403, 'An active subscription is required.');
    }
}
