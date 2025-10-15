<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response|RedirectResponse)  $next
     */
    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        /** @var User|null $user */
        $user = $request->user();

        if ($user && $this->userHasPremiumAccess($user)) {
            return $next($request);
        }

        return redirect()
            ->route('account')
            ->with('error', 'A current subscription is required to browse your watch history.');
    }

    /**
     * Determine if the user has an active or grace-period subscription.
     */
    protected function userHasPremiumAccess(User $user): bool
    {
        $subscriptionName = 'default';

        if ($user->subscribed($subscriptionName)) {
            return true;
        }

        if ($user->onTrial($subscriptionName)) {
            return true;
        }

        $subscription = $user->subscription($subscriptionName);

        return (bool) ($subscription?->onGracePeriod());
    }
}
