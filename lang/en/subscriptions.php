<?php

return [
    'status' => [
        'already_subscribed' => 'You already have an active subscription.',
        'cancellation_scheduled' => 'Your subscription has been cancelled and will remain active until :date.',
        'cancelled' => 'Your subscription has been cancelled.',
        'resumed' => 'Your subscription has been resumed successfully.',
    ],
    'errors' => [
        'price_required' => 'A Stripe price identifier is required to start your trial.',
        'missing_subscription_cancel' => 'You do not have an active subscription to cancel.',
        'already_cancelled' => 'Your subscription is already cancelled.',
        'cancel_failed' => 'We were unable to cancel your subscription. Please try again.',
        'missing_subscription_resume' => 'You do not have an active subscription to resume.',
        'not_on_grace_period' => 'Your subscription cannot be resumed because it is not within the grace period.',
        'resume_failed' => 'We were unable to resume your subscription. Please try again.',
        'invoice_load_failed' => 'We were unable to load your upcoming invoice.',
        'premium_required' => 'A premium subscription is required to access this area.',
        'access_required' => 'An active subscription is required to access this area.',
    ],
];
