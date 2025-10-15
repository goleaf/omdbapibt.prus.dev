<?php

namespace App\Http\Middleware;

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

        if ($user && ($user->subscribed() || $user->onTrial())) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            abort(403, 'An active subscription is required to access this resource.');
        }

        return redirect()
            ->route('billing.portal')
            ->with('error', 'You need an active subscription to access the requested page.');
    }
}
