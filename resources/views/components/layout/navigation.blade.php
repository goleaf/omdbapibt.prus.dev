@props([
    'user' => auth()->user(),
])

@php
    $hasLogin = Route::has('login');
    $hasRegister = Route::has('register');
@endphp

<header {{ $attributes->merge(['class' => 'surface-shell border-b sticky top-0 z-50']) }}>
    <div class="mx-auto flex w-full max-w-screen-2xl items-center justify-between gap-4 px-6 py-4 2xl:px-12">
        
        <!-- Left: Mobile toggle + Logo -->
        <div class="flex items-center gap-4">
            <x-header.mobile-toggle />
            <x-header.logo />
        </div>

        <!-- Center: Desktop Navigation + Livewire Search -->
        <div class="hidden items-center gap-6 lg:flex">
            <x-navigation-links layout="horizontal" />
            @livewire('header.search-bar')
        </div>

        <!-- Right: Livewire User Menu or Static Auth -->
        <div class="flex items-center gap-3">
            @auth
                @livewire('header.user-menu')
            @else
                <x-header.auth-buttons :hasLogin="$hasLogin" :hasRegister="$hasRegister" class="hidden md:flex" />
            @endauth
        </div>
    </div>

    <!-- Livewire Mobile Panel -->
    @livewire('header.mobile-panel')
</header>
