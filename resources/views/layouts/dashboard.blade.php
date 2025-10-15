@php
    $pageTitle = $title ?? 'Dashboard';
    $pageHeader = $header ?? $pageTitle;
    $pageSubheader = $subheader ?? 'Manage your membership, preferences, and activity in one place.';
    $localeParameter = request()->route('locale');
    $isLocalized = request()->routeIs('localized.*');
    $baseParameters = $isLocalized && $localeParameter ? ['locale' => $localeParameter] : [];
    $navigation = [
        [
            'name' => 'account',
            'label' => 'Account overview',
            'description' => 'Profile, preferences, and security',
        ],
        [
            'name' => 'watch-history',
            'label' => 'Watch history',
            'description' => 'Browse your recent viewing activity',
        ],
    ];
@endphp

@extends('layouts.app', [
    'title' => $pageTitle,
    'header' => $pageHeader,
    'subheader' => $pageSubheader,
])

@section('content')
    <div class="grid gap-6 lg:grid-cols-[16rem,1fr]">
        <aside class="space-y-6">
            <div class="rounded-xl border border-slate-800/60 bg-slate-900/60 p-6 shadow-sm shadow-slate-950/30">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-400">Dashboard</h2>
                <p class="mt-2 text-sm text-slate-500">Quick links to everything related to your membership.</p>
            </div>

            <nav class="flex flex-col gap-2">
                @foreach ($navigation as $item)
                    @php
                        $routeName = $isLocalized ? 'localized.'.$item['name'] : $item['name'];
                        $isActive = request()->routeIs($item['name']) || request()->routeIs('localized.'.$item['name']);
                        $url = route($routeName, $baseParameters);
                    @endphp

                    <a
                        href="{{ $url }}"
                        class="group rounded-lg border border-slate-800/60 px-4 py-3 transition {{ $isActive ? 'border-emerald-400/80 bg-emerald-500/10 text-emerald-200' : 'bg-slate-900/40 text-slate-200 hover:border-emerald-400/40 hover:text-emerald-100' }}"
                    >
                        <span class="block text-sm font-semibold">{{ $item['label'] }}</span>
                        <span class="mt-1 block text-xs text-slate-400 group-hover:text-slate-300">{{ $item['description'] }}</span>
                    </a>
                @endforeach
            </nav>
        </aside>

        <div class="space-y-6">
            {{ $slot ?? '' }}
            @yield('dashboard-content')
        </div>
    </div>
@endsection
