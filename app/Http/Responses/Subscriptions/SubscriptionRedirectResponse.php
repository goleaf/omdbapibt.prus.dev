<?php

namespace App\Http\Responses\Subscriptions;

use Illuminate\Http\RedirectResponse;

class SubscriptionRedirectResponse
{
    public static function alreadySubscribed(): RedirectResponse
    {
        $locale = app()->getLocale() ?? config('app.fallback_locale');

        return redirect()->route('dashboard', ['locale' => $locale])
            ->with('status', __('subscriptions.status.already_subscribed'));
    }
}
