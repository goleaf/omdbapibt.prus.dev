<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && User::impersonatorId()) {
            abort(403, 'Impersonation mode cannot access administrative tools.');
        }

        if (! $user || ! $user->isAdmin()) {
            abort(403, 'This action is only available to administrators.');
        }

        return $next($request);
    }
}
