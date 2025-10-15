<?php

namespace App\Livewire;

use App\Models\User;
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
            session()->flash('error', __('subscriptions.errors.missing_subscription_cancel'));

            return;
        }

        if ($subscription->cancelled()) {
            session()->flash('error', __('subscriptions.errors.already_cancelled'));

            return;
        }

        try {
            $subscription->cancel();
        } catch (Throwable $exception) {
            report($exception);

            session()->flash('error', __('subscriptions.errors.cancel_failed'));

            return;
        }

        $this->refreshSubscriptionState();

        $endsAt = $this->getSubscription()?->ends_at;

        session()->flash(
            'status',
            $endsAt
                ? __('subscriptions.status.cancellation_scheduled', ['date' => $endsAt->toFormattedDateString()])
                : __('subscriptions.status.cancelled')
        );
    }

    /**
     * Resume the customer's subscription while in its grace period.
     */
    public function resumeSubscription(): void
    {
        $subscription = $this->getSubscription();

        if (! $subscription) {
            session()->flash('error', __('subscriptions.errors.missing_subscription_resume'));

            return;
        }

        if (! $subscription->onGracePeriod()) {
            session()->flash('error', __('subscriptions.errors.not_on_grace_period'));

            return;
        }

        try {
            $subscription->resume();
        } catch (Throwable $exception) {
            report($exception);

            session()->flash('error', __('subscriptions.errors.resume_failed'));

            return;
        }

        $this->refreshSubscriptionState();

        session()->flash('status', __('subscriptions.status.resumed'));
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

            session()->flash('error', __('subscriptions.errors.invoice_load_failed'));

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

        return view('livewire.subscription-manage', [
            'subscription' => $subscription,
            'planName' => $subscription?->type ?? $subscription?->stripe_price,
            'onTrial' => (bool) $subscription?->onTrial(),
            'trialEndsAt' => $subscription?->trial_ends_at,
            'isCancelled' => (bool) $subscription?->cancelled(),
            'isOnGracePeriod' => (bool) $subscription?->onGracePeriod(),
        ]);
    }
}
