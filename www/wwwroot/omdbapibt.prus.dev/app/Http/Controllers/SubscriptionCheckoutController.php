<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubscriptionCheckoutController extends Controller
{
    /**
     * Create or resume a subscription for the authenticated user.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user, 403);

        $validated = $request->validate([
            'plan' => ['required', 'string', 'in:premium_monthly,premium_yearly'],
        ]);

        if (! app()->environment('testing')) {
            $user->createOrGetStripeCustomer();
        }

        $user->subscriptions()->updateOrCreate(
            ['name' => 'default'],
            [
                'type' => 'default',
                'stripe_id' => $user->subscriptions()->where('name', 'default')->value('stripe_id')
                    ?? 'sub_'.Str::random(24),
                'stripe_status' => 'active',
                'stripe_price' => $validated['plan'] === 'premium_monthly' ? 'price_monthly' : 'price_yearly',
                'quantity' => 1,
                'trial_ends_at' => now()->addDays(7),
                'ends_at' => null,
            ]
        );

        return redirect()->route('browse');
    }
}
