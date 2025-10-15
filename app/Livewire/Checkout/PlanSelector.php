<?php

namespace App\Livewire\Checkout;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Features\SupportRedirects\Redirector;

class PlanSelector extends Component
{
    /**
     * The plans available for selection.
     *
     * @var array<string, array<string, mixed>>
     */
    public array $plans = [];

    protected ?User $user = null;

    public function boot(): void
    {
        $user = Auth::user();

        if ($user instanceof User) {
            $this->user = $user;
        }
    }

    public function mount(): ?Redirector
    {
        if (! $this->user instanceof User) {
            return $this->redirectRoute('login', ['locale' => app()->getLocale()]);
        }

        if ($this->user->hasPremiumAccess()) {
            session()->flash('status', __('subscriptions.status.already_subscribed'));

            return $this->redirectRoute('browse', ['locale' => app()->getLocale()]);
        }

        $plans = config('subscriptions.plans', []);

        $this->plans = collect($plans)
            ->filter(fn (array $plan): bool => filled(data_get($plan, 'price_id')))
            ->all();

        return null;
    }

    public function startCheckout(string $planKey): void
    {
        if (! array_key_exists($planKey, $this->plans)) {
            return;
        }

        $priceId = (string) ($this->plans[$planKey]['price_id'] ?? '');

        if ($priceId === '') {
            session()->flash('error', __('subscriptions.errors.price_required'));

            return;
        }

        $this->dispatch('subscriptions.store', priceId: $priceId);
    }

    public function render(): View
    {
        return view('livewire.checkout.plan-selector')
            ->layout('layouts.app', [
                'title' => __('Upgrade your OMDb access'),
                'header' => __('Choose your cinematic pass'),
                'subheader' => __('Select a plan to unlock unlimited detail pages, streaming providers, and personal recommendations.'),
            ]);
    }
}
