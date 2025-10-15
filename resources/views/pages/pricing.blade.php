@extends('layouts.app', [
    'title' => 'Pricing',
    'header' => 'Choose the plan that unlocks every detail',
    'subheader' => 'Monthly or yearly subscriptions with full OMDb + TMDb data, streaming availability, and personalized discovery.',
])

@section('content')
    <section class="grid gap-8 lg:grid-cols-3">
        @foreach ([
            [
                'name' => 'Free',
                'price' => '$0',
                'frequency' => 'forever',
                'cta' => 'Create free account',
                'highlight' => false,
                'features' => [
                    'Browse trending movies and shows',
                    'Limited metadata (title, year, synopsis)',
                    'Save up to 10 watchlist items',
                    'Community reviews preview',
                ],
            ],
            [
                'name' => 'Premium Monthly',
                'price' => '$9.99',
                'frequency' => 'per month',
                'cta' => 'Start 7-day trial',
                'highlight' => true,
                'features' => [
                    'Full metadata (cast, crew, streaming, trailers)',
                    'Unlimited watchlist and history tracking',
                    'Personalized recommendations',
                    'Priority support and release alerts',
                ],
            ],
            [
                'name' => 'Premium Yearly',
                'price' => '$99.99',
                'frequency' => 'per year',
                'cta' => 'Switch to yearly',
                'highlight' => false,
                'features' => [
                    'All premium monthly features',
                    'Two bonus guest passes',
                    'Exclusive festival coverage',
                    'Save 17% compared to monthly',
                ],
            ],
        ] as $plan)
            <div @class([
                'rounded-3xl border bg-slate-900/70 p-8 shadow-lg shadow-slate-950/30',
                'border-emerald-500/80 ring-4 ring-emerald-500/20' => $plan['highlight'],
                'border-slate-800' => ! $plan['highlight'],
            ])>
                <p class="text-sm uppercase tracking-widest text-emerald-400">{{ $plan['name'] }}</p>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-4xl font-bold text-slate-50">{{ $plan['price'] }}</span>
                    <span class="text-sm text-slate-400">{{ $plan['frequency'] }}</span>
                </div>
                <p class="mt-4 text-sm text-slate-300">{{ $plan['highlight'] ? 'Best for superfans and power researchers.' : 'Great for exploring the platform and staying in the loop.' }}</p>
                <a href="#" class="mt-6 inline-flex w-full justify-center rounded-full px-5 py-3 text-sm font-semibold transition {{ $plan['highlight'] ? 'bg-emerald-500 text-emerald-950 hover:bg-emerald-400' : 'border border-slate-700 text-slate-200 hover:border-emerald-400 hover:text-emerald-200' }}">{{ $plan['cta'] }}</a>
                <ul class="mt-6 space-y-3 text-sm text-slate-200">
                    @foreach ($plan['features'] as $feature)
                        <li class="flex items-start gap-3">
                            <svg class="mt-0.5 h-4 w-4 text-emerald-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                <path fill-rule="evenodd" d="M2.25 12a9.75 9.75 0 1 1 19.5 0 9.75 9.75 0 0 1-19.5 0Zm13.1-3.53a.75.75 0 0 0-1.2-.9L11 11.234 9.6 9.985a.75.75 0 1 0-1 1.12l2 1.75a.75.75 0 0 0 1.07-.08Z" clip-rule="evenodd" />
                            </svg>
                            {{ $feature }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </section>
@endsection
