<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SubscriptionController extends Controller
{
    /**
     * Create a new subscription checkout session with a configured trial.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->subscribed('default')) {
            return redirect()
                ->route('dashboard')
                ->with('status', __('messages.subscription.already_active'));
        }

        $validated = $request->validate([
            'price' => ['required', 'string'],
        ]);

        $price = $validated['price'];

        if (empty($price)) {
            throw ValidationException::withMessages([
                'price' => __('messages.subscription.price_required'),
            ]);
        }

        $trialDays = (int) config('services.stripe.trial_days', 7);

        return $user->newSubscription('default', $price)
            ->trialDays(max($trialDays, 0))
            ->checkout([
                'success_url' => route('dashboard').'?checkout=success',
                'cancel_url' => url()->previous() ?: route('dashboard'),
                'metadata' => [
                    'type' => 'premium',
                    'name' => 'default',
                    'user_id' => $user->getAuthIdentifier(),
                ],
            ]);
    }
}
