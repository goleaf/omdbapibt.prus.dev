<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSubscriptionAccess
{
    public function __construct(private Translator $translator)
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response|RedirectResponse)  $next
     */
    public function handle(Request $request, Closure $next, string $subscription = 'default'): Response|RedirectResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return redirect()->route('login');
        }

        if ($user->hasPremiumAccess($subscription)) {
            return $next($request);
        }

        return redirect()->route('checkout')->with(
            'error',
            $this->translator->get('subscriptions.errors.access_required')
        );
    }
}
