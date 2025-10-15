<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} &mdash; {{ __('ui.dashboard.title') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="antialiased bg-gray-100 text-gray-900">
    <div class="min-h-screen">
        <header class="bg-white shadow">
            <div class="mx-auto max-w-7xl px-6 py-4 flex items-center justify-between">
                <h1 class="text-2xl font-semibold">{{ __('ui.dashboard.title') }}</h1>
                <nav class="flex items-center gap-4 text-sm font-medium">
                    <a class="text-blue-600 hover:text-blue-500" href="{{ route('dashboard') }}">{{ __('ui.dashboard.nav_overview') }}</a>
                    <a class="text-blue-600 hover:text-blue-500" href="{{ route('billing.portal') }}">
                        {{ __('ui.dashboard.nav_manage_subscription') }}
                    </a>
                </nav>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-6 py-10 space-y-6">
            <section class="rounded-lg bg-white p-6 shadow">
                <h2 class="text-xl font-semibold mb-2">{{ __('ui.dashboard.welcome_heading') }}</h2>
                <p class="text-gray-600">
                    {{ __('ui.dashboard.welcome_copy') }}
                </p>

                @php
                    $user = auth()->user();
                    $subscription = $user?->subscription('default');
                    $trialEndsAt = $subscription?->trial_ends_at?->setTimezone(config('app.timezone'));
                    $graceEndsAt = $subscription?->ends_at?->setTimezone(config('app.timezone'));
                    $trialDays = (int) config('services.stripe.trial_days', 7);
                    $priceId = config('services.stripe.prices.monthly');
                @endphp

                @if (session('status'))
                    <div class="mt-4 rounded-md border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-700">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($subscription?->onTrial())
                    <div class="mt-4 rounded-md border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                        <p class="font-medium">{{ __('ui.dashboard.trial_active_heading') }}</p>
                        <p class="mt-1">
                            {!! __('ui.dashboard.trial_active_copy', [
                                'date' => '<span class="font-semibold">' . e($trialEndsAt?->toDayDateTimeString()) . '</span>',
                            ]) !!}
                        </p>
                    </div>
                @elseif (! $subscription)
                    <div class="mt-4 rounded-md border border-dashed border-amber-300 bg-amber-50 px-4 py-4 text-sm text-amber-900">
                        <p class="font-medium">{{ __('ui.dashboard.trial_start_heading', ['days' => $trialDays]) }}</p>
                        <p class="mt-1">
                            {{ __('ui.dashboard.trial_start_copy') }}
                        </p>

                        @if ($priceId)
                            <form method="POST" action="{{ route('subscriptions.store') }}" class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center">
                                @csrf
                                <input type="hidden" name="price" value="{{ $priceId }}">
                                <button type="submit" class="inline-flex items-center justify-center rounded-md bg-amber-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-amber-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-600">
                                    {{ __('ui.dashboard.trial_start_button', ['days' => $trialDays]) }}
                                </button>
                            </form>
                        @else
                            <p class="mt-4 text-sm text-amber-800">
                                {!! __('ui.dashboard.trial_price_required', [
                                    'env_key' => '<code class="rounded bg-amber-100 px-1">STRIPE_MONTHLY_PRICE</code>',
                                ]) !!}
                            </p>
                        @endif

                        @error('price')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        <p class="mt-2 text-xs text-amber-700">{{ __('ui.dashboard.trial_cancel_hint') }}</p>
                    </div>
                @elseif ($subscription->active())
                    <div class="mt-4 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                        <p class="font-medium">{{ __('ui.dashboard.subscriber_heading') }}</p>
                        <p class="mt-1">{{ __('ui.dashboard.subscriber_copy') }}</p>
                    </div>
                @elseif ($subscription->onGracePeriod())
                    <div class="mt-4 rounded-md border border-yellow-200 bg-yellow-50 px-4 py-3 text-sm text-yellow-900">
                        <p class="font-medium">{{ __('ui.dashboard.grace_heading') }}</p>
                        <p class="mt-1">
                            {!! __('ui.dashboard.grace_copy', [
                                'date' => '<span class="font-semibold">' . e($graceEndsAt?->toDayDateTimeString()) . '</span>',
                            ]) !!}
                        </p>
                    </div>
                @else
                    <div class="mt-4 rounded-md border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700">
                        <p class="font-medium">{{ __('ui.dashboard.inactive_heading') }}</p>
                        <p class="mt-1">{{ __('ui.dashboard.inactive_copy') }}</p>
                    </div>
                @endif
            </section>

            @livewire('subscription-manage')
        </main>
    </div>
    @livewireScripts
</body>
</html>
