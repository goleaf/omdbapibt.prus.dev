<?php

namespace App\Support\Subscriptions;

use App\Models\User;
use Illuminate\Support\Carbon;

class BillingState
{
    public function __construct(protected User $user, protected string $subscription = 'default') {}

    public function user(): User
    {
        return $this->user;
    }

    public function subscriptionType(): string
    {
        return $this->subscription;
    }

    public function hasActiveSubscription(): bool
    {
        return $this->user->hasPremiumAccess($this->subscription);
    }

    public function canAccessPortal(): bool
    {
        return $this->user->canAccessBillingPortal($this->subscription);
    }

    public function trialDaysRemaining(): int
    {
        $trialEndsAt = $this->user->trialEndsAt($this->subscription);

        if (! $trialEndsAt instanceof Carbon) {
            return 0;
        }

        $remaining = now()->startOfDay()->diffInDays($trialEndsAt, false);

        return max(0, $remaining);
    }
}
