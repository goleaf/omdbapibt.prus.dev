<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user instanceof User) {
            abort(403, 'You must be signed in to access this content.');
        }

        $subscription = $user->subscription('default');

        if ($subscription && ($subscription->active() || $subscription->onGracePeriod())) {
            return $next($request);
        }

        if ($user->onTrial() || $user->onTrial('default')) {
            return $next($request);
        }

        abort(403, 'An active subscription is required to access this content.');
    }
}
