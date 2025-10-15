<?php

namespace App\Livewire\Checkout;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Upgrade your OMDb access')]
class PlanSelector extends Component
{
    /**
     * @var array<string, string>
     */
    public array $plans = [];

    public function mount(): void
    {
        $user = Auth::user();

        if (! $user) {
            $this->redirectRoute('login', ['locale' => app()->getLocale()]);

            return;
        }

        if ($user->hasPremiumAccess()) {
            session()->flash('status', __('subscriptions.status.already_subscribed'));

            $this->redirectRoute('browse', ['locale' => app()->getLocale()]);

            return;
        }

        $this->plans = array_filter([
            'monthly' => (string) config('services.stripe.prices.monthly'),
            'yearly' => (string) config('services.stripe.prices.yearly'),
        ]);
    }

    public function selectPlan(string $priceId): void
    {
        if (! in_array($priceId, $this->plans, true)) {
            return;
        }

        $this->dispatch('subscriptions.store', price: $priceId);
    }

    public function render(): View
    {
        return view('livewire.checkout.plan-selector', [
            'plans' => $this->plans,
        ])->layout('layouts.app', [
            'title' => __('Upgrade your OMDb access'),
            'header' => __('Choose your cinematic pass'),
            'subheader' => __('Select a plan to unlock unlimited detail pages, streaming providers, and personal recommendations.'),
        ]);
    }
}
