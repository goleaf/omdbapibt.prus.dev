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
    public function __construct(private Translator $translator) {}

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response|RedirectResponse)  $next
     */
    public function handle(Request $request, Closure $next, string $subscription = 'default'): Response|RedirectResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return redirect()->to(localized_route('login'));
        }

        if ($user->hasPremiumAccess($subscription)) {
            return $next($request);
        }

        $message = $this->translator->get(
            'messages.subscription.access_required',
            [],
            config('app.fallback_locale')
        );

        return redirect()->to(localized_route('checkout'))->with('error', $message);
    }
}
