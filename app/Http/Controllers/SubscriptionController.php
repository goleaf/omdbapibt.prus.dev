<?php

namespace App\Http\Controllers;

use App\Http\Requests\Subscriptions\StoreSubscriptionRequest;
use App\Http\Responses\Subscriptions\SubscriptionRedirectResponse;
use Illuminate\Http\RedirectResponse;

class SubscriptionController extends Controller
{
    /**
     * Create a new subscription checkout session with a configured trial.
     */
    public function store(StoreSubscriptionRequest $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->subscribed('default')) {
            return SubscriptionRedirectResponse::alreadySubscribed();
        }

        $price = $request->price();

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
