@extends('layouts.app', [
    'title' => config('app.name', 'OMDb Stream') . ' — ' . __('ui.dashboard.title'),
    'header' => __('ui.dashboard.title'),
])

@section('content')
    <div class="space-y-12">
        <x-flux.card heading="{{ __('ui.dashboard.welcome_heading') }}" subheading="{{ __('ui.dashboard.welcome_body') }}" elevated>
            @php
                $user = auth()->user();
                $subscription = $user?->subscription('default');
                $trialEndsAt = $subscription?->trial_ends_at?->setTimezone(config('app.timezone'));
                $graceEndsAt = $subscription?->ends_at?->setTimezone(config('app.timezone'));
                $trialDays = (int) config('services.stripe.trial_days', 7);
                $priceId = config('services.stripe.prices.monthly');
            @endphp

            @if (session('status'))
                <div class="rounded-2xl border border-emerald-400/50 bg-emerald-500/15 px-4 py-3 text-sm text-emerald-100">
                    {{ session('status') }}
                </div>
            @endif

            <div class="grid gap-6 md:grid-cols-2">
                <div class="space-y-4 text-sm text-slate-200">
                    @if ($subscription?->onTrial())
                        <div class="rounded-2xl border border-amber-300/60 bg-amber-400/15 p-5 text-amber-100">
                            <p class="font-semibold">{{ __('ui.dashboard.trial.active_title') }}</p>
                            <p class="mt-1">
                                {!! __('ui.dashboard.trial.active_body', ['date' => '<span class="font-semibold">'.$trialEndsAt?->toDayDateTimeString().'</span>']) !!}
                            </p>
                        </div>
                    @elseif (! $subscription)
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
                    @elseif ($subscription->active())
                        <div class="rounded-2xl border border-emerald-400/50 bg-emerald-500/15 p-5 text-emerald-100">
                            <p class="font-semibold">{{ __('ui.dashboard.subscriber.thanks_title') }}</p>
                            <p class="mt-1">{{ __('ui.dashboard.subscriber.thanks_body') }}</p>
                        </div>
                    @elseif ($subscription->onGracePeriod())
                        <div class="rounded-2xl border border-yellow-300/60 bg-yellow-400/15 p-5 text-yellow-100">
                            <p class="font-semibold">{{ __('ui.dashboard.grace.title') }}</p>
                            <p class="mt-1">
                                {!! __('ui.dashboard.grace.body', ['date' => '<span class="font-semibold">'.$graceEndsAt?->toDayDateTimeString().'</span>']) !!}
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
                        <p class="text-xs uppercase tracking-[0.35em] text-emerald-200">Plan insights</p>
                        <dl class="mt-4 grid gap-3 text-sm text-slate-300">
                            <div class="flex items-center justify-between">
                                <dt>Subscription status</dt>
                                <dd class="font-semibold text-emerald-200">{{ $subscription?->status ?? 'inactive' }}</dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt>Trial days</dt>
                                <dd>{{ $trialDays }}</dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt>Next invoice</dt>
                                <dd>{{ optional($subscription?->nextPaymentAttempt())->toDateTimeString() ?? '—' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </x-flux.card>

        <livewire:dashboard.recommendations />

        <x-flux.card heading="Manage subscription">
            @livewire('subscription-manage')
        </x-flux.card>

        <x-flux.card heading="Watchlist">
            @livewire('watchlist')
        </x-flux.card>
    </div>
@endsection
