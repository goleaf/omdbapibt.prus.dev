<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user) {
            return redirect()
                ->route('signup')
                ->with('status', __('Create an account to continue to checkout.'));
        }

        if ($user->subscribed('default')) {
            return redirect()
                ->route('browse')
                ->with('status', __('You already have an active subscription.'));
        }

        return view('pages.subscriptions.checkout', [
            'monthlyPrice' => config('services.stripe.prices.monthly'),
            'yearlyPrice' => config('services.stripe.prices.yearly'),
            'trialDays' => (int) config('services.stripe.trial_days', 7),
        ]);
    }

    /**
     * Create a new subscription checkout session with a configured trial.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->subscribed('default')) {
            return redirect()
                ->route('browse')
                ->with('status', __('You already have an active subscription.'));
        }

        $validated = $request->validate([
            'price' => ['required', 'string'],
        ]);

        $price = $validated['price'];

        if (empty($price)) {
            throw ValidationException::withMessages([
                'price' => __('A Stripe price identifier is required to start your trial.'),
            ]);
        }

        $trialDays = (int) config('services.stripe.trial_days', 7);

        return $user->newSubscription('default', $price)
            ->trialDays(max($trialDays, 0))
            ->checkout([
                'success_url' => route('browse').'?checkout=success',
                'cancel_url' => route('subscriptions.checkout'),
                'metadata' => [
                    'type' => 'premium',
                    'name' => 'default',
                    'user_id' => $user->getAuthIdentifier(),
                ],
            ]);
    }
}
