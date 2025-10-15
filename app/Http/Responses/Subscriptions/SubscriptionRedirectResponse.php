<?php

namespace App\Http\Responses\Subscriptions;

use Illuminate\Http\RedirectResponse;

class SubscriptionRedirectResponse
{
    public static function alreadySubscribed(): RedirectResponse
    {
        return redirect()
            ->route('dashboard')
            ->with('status', __('subscriptions.status.already_subscribed'));
    }
}
