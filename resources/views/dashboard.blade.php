@extends('layouts.app', [
    'title' => config('app.name', 'OMDb Stream') . ' — ' . __('ui.dashboard.title'),
    'header' => __('ui.dashboard.title'),
])

@section('content')
    <div class="space-y-12">
        <x-flux.card heading="{{ __('ui.dashboard.welcome_heading') }}" subheading="{{ __('ui.dashboard.welcome_body') }}" elevated>
            @if ($statusMessage)
                <div class="rounded-2xl border border-emerald-400/50 bg-emerald-500/15 px-4 py-3 text-sm text-emerald-100">
                    {{ $statusMessage }}
                </div>
            @endif

            <div class="grid gap-6 md:grid-cols-2">
                <div class="space-y-4 text-sm text-slate-200">
                    @if ($subscriptionState === 'trial')
                        <div class="rounded-2xl border border-amber-300/60 bg-amber-400/15 p-5 text-amber-100">
                            <p class="font-semibold">{{ __('ui.dashboard.trial.active_title') }}</p>
                            <p class="mt-1">
                                {!! __('ui.dashboard.trial.active_body', ['date' => '<span class="font-semibold">'.$trialEndsAtLabel.'</span>']) !!}
                            </p>
                        </div>
                    @elseif ($subscriptionState === 'none')
                        <div class="rounded-2xl border border-dashed border-amber-300/60 bg-amber-400/10 p-5 text-amber-100">
                            <p class="font-semibold">{{ __('ui.dashboard.trial.intro_title', ['days' => $trialDays]) }}</p>
                            <p class="mt-2">{{ __('ui.dashboard.trial.intro_body') }}</p>

                            @if ($priceId)
                                <form method="POST" action="{{ route('subscriptions.store') }}" class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center">
                                    @csrf
                                    <input type="hidden" name="price" value="{{ $priceId }}">
                                    <flux:button type="submit" variant="primary" color="emerald">
                                        {{ __('ui.dashboard.trial.cta', ['days' => $trialDays]) }}
                                    </flux:button>
                                </form>
                            @else
                                <p class="mt-4 text-xs">
                                    {!! __('ui.dashboard.trial.missing_price', ['key' => '<code class="rounded bg-amber-500/20 px-1">STRIPE_MONTHLY_PRICE</code>']) !!}
                                </p>
                            @endif

                            @error('price')
                                <p class="mt-2 text-xs text-red-300">{{ $message }}</p>
                            @enderror

                            <p class="mt-2 text-[0.7rem] uppercase tracking-[0.4em]">{{ __('ui.dashboard.trial.cancel_notice') }}</p>
                        </div>
                    @elseif ($subscriptionState === 'active')
                        <div class="rounded-2xl border border-emerald-400/50 bg-emerald-500/15 p-5 text-emerald-100">
                            <p class="font-semibold">{{ __('ui.dashboard.subscriber.thanks_title') }}</p>
                            <p class="mt-1">{{ __('ui.dashboard.subscriber.thanks_body') }}</p>
                        </div>
                    @elseif ($subscriptionState === 'grace')
                        <div class="rounded-2xl border border-yellow-300/60 bg-yellow-400/15 p-5 text-yellow-100">
                            <p class="font-semibold">{{ __('ui.dashboard.grace.title') }}</p>
                            <p class="mt-1">
                                {!! __('ui.dashboard.grace.body', ['date' => '<span class="font-semibold">'.$graceEndsAtLabel.'</span>']) !!}
                            </p>
                        </div>
                    @else
                        <div class="rounded-2xl border border-slate-700/70 bg-slate-900/60 p-5 text-slate-300">
                            <p class="font-semibold">{{ __('ui.dashboard.inactive.title') }}</p>
                            <p class="mt-1">{{ __('ui.dashboard.inactive.body') }}</p>
                        </div>
                    @endif
                </div>

                <div class="space-y-4">
                    <div class="rounded-2xl border border-slate-800/60 bg-slate-900/70 p-5">
                        <p class="text-xs uppercase tracking-[0.35em] text-emerald-200">{{ __('ui.dashboard.insights_card.title') }}</p>
                        <dl class="mt-4 grid gap-3 text-sm text-slate-300">
                            <div class="flex items-center justify-between">
                                <dt>{{ __('ui.dashboard.insights_card.subscription_status') }}</dt>
                                <dd class="font-semibold text-emerald-200">{{ $subscriptionStatus }}</dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt>{{ __('ui.dashboard.insights_card.trial_days') }}</dt>
                                <dd>{{ $trialDays }}</dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt>{{ __('ui.dashboard.insights_card.next_invoice') }}</dt>
                                <dd>{{ $nextInvoiceLabel }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </x-flux.card>

        <livewire:dashboard.recommendations />

        <x-flux.card heading="{{ __('ui.dashboard.cards.manage_subscription') }}">
            @livewire('subscription-manage')
        </x-flux.card>

        <x-flux.card heading="{{ __('ui.dashboard.cards.watchlist') }}">
            @livewire('watchlist')
        </x-flux.card>
    </div>
@endsection
