<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Laravel\Cashier\Subscription as CashierSubscription;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = auth()->user();

        /** @var CashierSubscription|null $subscription */
        $subscription = $user?->subscription('default');

        $timezone = Config::get('app.timezone');

        $trialEndsAt = $subscription?->trial_ends_at?->setTimezone($timezone);
        $graceEndsAt = $subscription?->ends_at?->setTimezone($timezone);
        $nextInvoice = $subscription?->nextPaymentAttempt();

        return view('dashboard', [
            'statusMessage' => Session::get('status'),
            'subscriptionState' => $this->resolveSubscriptionState($subscription),
            'subscriptionStatus' => $subscription?->status ?? 'inactive',
            'trialDays' => (int) Config::get('services.stripe.trial_days', 7),
            'priceId' => Config::get('services.stripe.prices.monthly'),
            'trialEndsAtLabel' => $this->formatDate($trialEndsAt) ?? '—',
            'graceEndsAtLabel' => $this->formatDate($graceEndsAt) ?? '—',
            'nextInvoiceLabel' => $this->formatDateTime($nextInvoice) ?? '—',
        ]);
    }

    private function resolveSubscriptionState(?CashierSubscription $subscription): string
    {
        if (! $subscription) {
            return 'none';
        }

        if ($subscription->onTrial()) {
            return 'trial';
        }

        if ($subscription->onGracePeriod()) {
            return 'grace';
        }

        if ($subscription->active()) {
            return 'active';
        }

        return 'inactive';
    }

    private function formatDate(?Carbon $date): ?string
    {
        return $date?->toDayDateTimeString();
    }

    private function formatDateTime(?Carbon $dateTime): ?string
    {
        return $dateTime?->toDateTimeString();
    }
}
