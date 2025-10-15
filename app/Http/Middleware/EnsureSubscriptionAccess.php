<?php

namespace App\Http\Middleware;

use App\Models\Movie;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class EnsureSubscriptionAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('signup');
        }

        try {
            Gate::forUser($user)->authorize('viewAny', Movie::class);
        } catch (AuthorizationException $exception) {
            return redirect()
                ->route('pricing')
                ->with('status', __('A subscription is required to access this section.'));
        }

        return $next($request);
    }
}
