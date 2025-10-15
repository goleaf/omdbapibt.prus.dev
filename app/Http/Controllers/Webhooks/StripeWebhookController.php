<?php

namespace App\Http\Controllers\Webhooks;

use Laravel\Cashier\Http\Controllers\WebhookController as CashierWebhookController;

/**
 * Handle incoming Stripe webhook calls and synchronize subscription state
 * using Laravel Cashier's base webhook controller.
 */
class StripeWebhookController extends CashierWebhookController
{
    /**
     * Determine the subscription name that should be stored when Stripe does
     * not provide an explicit "type" value.
     */
    protected function newSubscriptionType(array $payload)
    {
        $data = $payload['data']['object'] ?? [];

        return $data['metadata']['type']
            ?? $data['metadata']['name']
            ?? parent::newSubscriptionType($payload);
    }
}
