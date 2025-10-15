<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Cashier\Cashier;
use Laravel\Cashier\Subscription;
use Livewire\Component;
use Stripe\Exception\ApiErrorException;
use Throwable;

class SubscriptionManage extends Component
{
    /**
     * The Stripe invoice payload for the customer's upcoming invoice.
     *
     * @var array{amount_due:int,currency:string,period_end:int,period_start:int}|null
     */
    public ?array $upcomingInvoice = null;

    /**
     * The name of the subscription we are managing.
     */
    protected string $subscriptionName = 'default';

    /**
     * Cached subscription instance for the current request cycle.
     */
    protected ?Subscription $currentSubscription = null;

    /**
     * Ensure the authenticated user has a Stripe customer and preload data.
     */
    public function mount(): void
    {
        $user = $this->user();

        if (blank(config('cashier.secret'))) {
            if ($name = $user->subscriptions()->value('name')) {
                $this->subscriptionName = $name;
            }

            return;
        }

        $user->createOrGetStripeCustomer();

        if ($name = $user->subscriptions()->value('name')) {
            $this->subscriptionName = $name;
        }

        $this->refreshSubscriptionState();
    }

    /**
     * Cancel the customer's subscription at the end of the billing cycle.
     */
    public function cancelSubscription(): void
    {
        $subscription = $this->getSubscription();

        if (! $subscription) {
            session()->flash('error', 'You do not have an active subscription to cancel.');

            return;
        }

        if ($subscription->cancelled()) {
            session()->flash('error', 'Your subscription is already cancelled.');

            return;
        }

        try {
            $subscription->cancel();
        } catch (Throwable $exception) {
            report($exception);

            session()->flash('error', 'We were unable to cancel your subscription. Please try again.');

            return;
        }

        $this->refreshSubscriptionState();

        $endsAt = $this->getSubscription()?->ends_at;

        session()->flash(
            'status',
            $endsAt
                ? 'Your subscription has been cancelled and will remain active until '.$endsAt->toFormattedDateString().'.'
                : 'Your subscription has been cancelled.'
        );
    }

    /**
     * Resume the customer's subscription while in its grace period.
     */
    public function resumeSubscription(): void
    {
        $subscription = $this->getSubscription();

        if (! $subscription) {
            session()->flash('error', 'You do not have an active subscription to resume.');

            return;
        }

        if (! $subscription->onGracePeriod()) {
            session()->flash('error', 'Your subscription cannot be resumed because it is not within the grace period.');

            return;
        }

        try {
            $subscription->resume();
        } catch (Throwable $exception) {
            report($exception);

            session()->flash('error', 'We were unable to resume your subscription. Please try again.');

            return;
        }

        $this->refreshSubscriptionState();

        session()->flash('status', 'Your subscription has been resumed successfully.');
    }

    /**
     * Re-fetch the subscription and invoice information.
     */
    protected function refreshSubscriptionState(): void
    {
        $this->currentSubscription = null;
        $this->upcomingInvoice = $this->retrieveUpcomingInvoice();
    }

    /**
     * Retrieve the subscription we are managing.
     */
    protected function getSubscription(): ?Subscription
    {
        if ($this->currentSubscription instanceof Subscription) {
            $this->currentSubscription = $this->currentSubscription->fresh(['items']);

            return $this->currentSubscription;
        }

        $subscription = $this->user()->subscription($this->subscriptionName);

        if ($subscription) {
            $subscription->loadMissing('items');
        }

        return $this->currentSubscription = $subscription;
    }

    /**
     * Attempt to load the upcoming invoice for the authenticated user.
     */
    protected function retrieveUpcomingInvoice(): ?array
    {
        $user = $this->user();
        $subscription = $this->getSubscription();

        if (blank(config('cashier.secret'))) {
            return null;
        }

        if (! $user->hasStripeId() || ! $subscription?->stripe_id) {
            return null;
        }

        try {
            $invoice = Cashier::stripe()->invoices->upcoming([
                'customer' => $user->stripe_id,
                'subscription' => $subscription->stripe_id,
            ]);
        } catch (ApiErrorException $exception) {
            report($exception);

            session()->flash('error', 'We were unable to load your upcoming invoice.');

            return null;
        }

        return [
            'amount_due' => (int) $invoice->amount_due,
            'currency' => Str::upper($invoice->currency ?? 'usd'),
            'period_start' => (int) ($invoice->period_start ?? $invoice->created ?? 0),
            'period_end' => (int) ($invoice->period_end ?? $invoice->due_date ?? $invoice->created ?? 0),
        ];
    }

    /**
     * Resolve the authenticated user instance for the current request.
     */
    protected function user(): User
    {
        $user = Auth::user();

        abort_unless($user instanceof User, 403);

        return $user;
    }

    public function render()
    {
        $subscription = $this->getSubscription();
        $planName = $subscription?->type ?? $subscription?->stripe_price;

        $statusLabel = 'Inactive';

        if ($subscription?->active()) {
            $statusLabel = 'Active';
        } elseif ($subscription?->onGracePeriod()) {
            $statusLabel = 'Grace period';
        } elseif ($subscription?->cancelled()) {
            $statusLabel = 'Cancelled';
        }

        $trialLabel = 'No active trial';

        if ($subscription?->onTrial()) {
            $trialLabel = $subscription->trial_ends_at
                ? 'Trial ends on '.$subscription->trial_ends_at->toFormattedDateString()
                : 'Trial active';
        }

        $gracePeriodEnds = $subscription?->onGracePeriod() && $subscription->ends_at
            ? $subscription->ends_at->toFormattedDateString()
            : null;

        $invoiceDetails = $this->formatUpcomingInvoiceDetails();

        return view('livewire.subscription-manage', [
            'subscription' => $subscription,
            'planDisplay' => $planName ?: 'Custom Plan',
            'quantity' => $subscription?->items->first()?->quantity ?? $subscription?->quantity ?? 1,
            'statusLabel' => $statusLabel,
            'trialLabel' => $trialLabel,
            'gracePeriodEnds' => $gracePeriodEnds,
            'invoiceDetails' => $invoiceDetails,
            'isCancelled' => (bool) $subscription?->cancelled(),
            'isOnGracePeriod' => (bool) $subscription?->onGracePeriod(),
        ]);
    }

    protected function formatUpcomingInvoiceDetails(): ?array
    {
        if ($this->upcomingInvoice === null) {
            return null;
        }

        $amount = number_format($this->upcomingInvoice['amount_due'] / 100, 2);
        $currency = $this->upcomingInvoice['currency'] ?? 'USD';

        $periodStart = $this->upcomingInvoice['period_start'] > 0
            ? Carbon::createFromTimestamp($this->upcomingInvoice['period_start'])->toFormattedDateString()
            : null;

        $periodEnd = $this->upcomingInvoice['period_end'] > 0
            ? Carbon::createFromTimestamp($this->upcomingInvoice['period_end'])->toFormattedDateString()
            : null;

        return [
            'amount' => $amount,
            'currency' => $currency,
            'charge_date' => $periodEnd,
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'period_range' => ($periodStart && $periodEnd) ? $periodStart.' – '.$periodEnd : null,
        ];
    }
}
