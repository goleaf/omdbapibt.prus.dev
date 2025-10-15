<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} &mdash; Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="antialiased bg-gray-100 text-gray-900">
    <div class="min-h-screen">
        <header class="bg-white shadow">
            <div class="mx-auto max-w-7xl px-6 py-4 flex items-center justify-between">
                <h1 class="text-2xl font-semibold">Dashboard</h1>
                <nav class="flex items-center gap-4 text-sm font-medium">
                    <a class="text-blue-600 hover:text-blue-500" href="{{ route('dashboard') }}">Overview</a>
                    <a class="text-blue-600 hover:text-blue-500" href="{{ route('billing.portal') }}">
                        Manage Subscription
                    </a>
                </nav>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-6 py-10 space-y-6">
            <section class="rounded-lg bg-white p-6 shadow">
                <h2 class="text-xl font-semibold mb-2">Welcome back!</h2>
                <p class="text-gray-600">
                    Review your plan details, manage billing, and make changes to your subscription in real time.
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
                        <p class="font-medium">Your free trial is active.</p>
                        <p class="mt-1">
                            Enjoy full access until
                            <span class="font-semibold">{{ $trialEndsAt?->toDayDateTimeString() }}</span>.
                            We'll send reminders before billing begins.
                        </p>
                    </div>
                @elseif (! $subscription)
                    <div class="mt-4 rounded-md border border-dashed border-amber-300 bg-amber-50 px-4 py-4 text-sm text-amber-900">
                        <p class="font-medium">Start your {{ $trialDays }}-day free trial.</p>
                        <p class="mt-1">
                            Unlock every movie detail, premium filters, and curated recommendations while you evaluate the platform.
                        </p>

                        @if ($priceId)
                            <form method="POST" action="{{ route('subscriptions.store') }}" class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center">
                                @csrf
                                <input type="hidden" name="price" value="{{ $priceId }}">
                                <button type="submit" class="inline-flex items-center justify-center rounded-md bg-amber-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-amber-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-600">
                                    Start {{ $trialDays }}-day trial
                                </button>
                            </form>
                        @else
                            <p class="mt-4 text-sm text-amber-800">
                                Add your Stripe price identifier to <code class="rounded bg-amber-100 px-1">STRIPE_MONTHLY_PRICE</code> to enable subscriptions.
                            </p>
                        @endif

                        @error('price')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        <p class="mt-2 text-xs text-amber-700">Cancel any time before the trial ends to avoid charges.</p>
                    </div>
                @elseif ($subscription->active())
                    <div class="mt-4 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                        <p class="font-medium">Thanks for being a subscriber!</p>
                        <p class="mt-1">Enjoy unlimited access to detailed data, watchlists, and personalized insights.</p>
                    </div>
                @elseif ($subscription->onGracePeriod())
                    <div class="mt-4 rounded-md border border-yellow-200 bg-yellow-50 px-4 py-3 text-sm text-yellow-900">
                        <p class="font-medium">Your subscription is scheduled to end.</p>
                        <p class="mt-1">
                            Access remains available until <span class="font-semibold">{{ $graceEndsAt?->toDayDateTimeString() }}</span>.
                            Resume the plan in Stripe if you change your mind.
                        </p>
                    </div>
                @else
                    <div class="mt-4 rounded-md border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700">
                        <p class="font-medium">Subscription inactive.</p>
                        <p class="mt-1">Re-subscribe anytime from the billing portal to regain premium access.</p>
                    </div>
                @endif
            </section>

            @livewire('dashboard-recommendations')

            @livewire('subscription-manage')
        </main>
    </div>
    @livewireScripts
</body>
</html>
