@extends('layouts.app', [
    'title' => 'Complete your subscription',
    'header' => 'Secure checkout',
    'subheader' => 'Confirm your plan and start streaming smarter in under a minute.',
])

@section('content')
    <div class="mx-auto max-w-4xl space-y-10">
        <section class="rounded-3xl border border-slate-800 bg-slate-900/70 p-8 shadow-lg shadow-emerald-500/5">
            <h2 class="text-lg font-semibold text-slate-100">Choose your membership</h2>
            <div class="mt-6 grid gap-6 md:grid-cols-2">
                <div class="rounded-2xl border border-slate-800 bg-slate-950/80 p-6">
                    <p class="text-sm font-semibold uppercase tracking-wide text-emerald-300">Monthly</p>
                    <p class="mt-3 text-3xl font-bold text-slate-50">{{ $monthlyPrice ? '$' . number_format($monthlyPrice / 100, 2) : 'Coming soon' }} <span class="text-base font-medium text-slate-400">per month</span></p>
                    <ul class="mt-5 space-y-3 text-sm text-slate-300">
                        <li>Instant access to the browse workspace and alerts.</li>
                        <li>Cancel anytime from your dashboard.</li>
                        <li>Applies your {{ $trialDays }} day free trial automatically.</li>
                    </ul>
                </div>
                <div class="rounded-2xl border border-emerald-500/50 bg-emerald-500/10 p-6">
                    <p class="text-sm font-semibold uppercase tracking-wide text-emerald-200">Best value</p>
                    <p class="mt-3 text-3xl font-bold text-slate-50">{{ $yearlyPrice ? '$' . number_format($yearlyPrice / 100, 2) : 'Coming soon' }} <span class="text-base font-medium text-slate-400">per year</span></p>
                    <ul class="mt-5 space-y-3 text-sm text-slate-200">
                        <li>Two months free compared to monthly billing.</li>
                        <li>Priority access to upcoming parser refreshes.</li>
                        <li>Applies your {{ $trialDays }} day free trial automatically.</li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="rounded-3xl border border-slate-800 bg-slate-950/80 p-8">
            <h2 class="text-lg font-semibold text-slate-100">Next steps</h2>
            <ol class="mt-6 space-y-4 text-sm text-slate-300">
                <li class="flex items-start gap-3">
                    <span class="mt-0.5 inline-flex h-6 w-6 items-center justify-center rounded-full bg-emerald-500/20 text-sm font-semibold text-emerald-300">1</span>
                    <div>
                        <p class="font-semibold text-slate-100">Select the plan above that suits you best.</p>
                        <p class="text-slate-400">The checkout modal will open securely in Stripe with your chosen price preloaded.</p>
                    </div>
                </li>
                <li class="flex items-start gap-3">
                    <span class="mt-0.5 inline-flex h-6 w-6 items-center justify-center rounded-full bg-emerald-500/20 text-sm font-semibold text-emerald-300">2</span>
                    <div>
                        <p class="font-semibold text-slate-100">Complete payment details.</p>
                        <p class="text-slate-400">Your trial will start immediately and no charges occur until it ends.</p>
                    </div>
                </li>
                <li class="flex items-start gap-3">
                    <span class="mt-0.5 inline-flex h-6 w-6 items-center justify-center rounded-full bg-emerald-500/20 text-sm font-semibold text-emerald-300">3</span>
                    <div>
                        <p class="font-semibold text-slate-100">Return to browse.</p>
                        <p class="text-slate-400">We'll automatically redirect you to the catalogue once checkout succeeds.</p>
                    </div>
                </li>
            </ol>
            <p class="mt-6 text-xs text-slate-500">Need help? Reach out to billing support before confirming payment so we can ensure you're on the right plan.</p>
        </section>
    </div>
@endsection
