@extends('layouts.app', [
    'title' => __('Sign in'),
    'header' => __('Welcome back'),
    'subheader' => __('Log in to manage your watchlist, recommendations, and premium features.'),
])

@section('content')
    <div class="mx-auto max-w-xl space-y-8 rounded-3xl border border-slate-800/60 bg-slate-900/70 p-8">
        <form method="POST" action="#" class="space-y-5">
            <div>
                <label for="email" class="text-sm font-semibold text-slate-200">{{ __('Email address') }}</label>
                <input id="email" name="email" type="email" class="mt-2 w-full rounded-2xl border border-slate-800 bg-slate-950 px-4 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none" placeholder="{{ __('you@example.com') }}">
            </div>
            <div>
                <label for="password" class="text-sm font-semibold text-slate-200">{{ __('Password') }}</label>
                <input id="password" name="password" type="password" class="mt-2 w-full rounded-2xl border border-slate-800 bg-slate-950 px-4 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none" placeholder="••••••••">
            </div>
            <div class="flex items-center justify-between text-xs text-slate-300">
                <label class="flex items-center gap-2">
                    <input type="checkbox" class="rounded border-slate-700 bg-slate-900">
                    <span>{{ __('Remember me') }}</span>
                </label>
                <a href="#" class="text-emerald-300 hover:text-emerald-200">{{ __('Forgot password?') }}</a>
            </div>
            <button type="submit" class="w-full rounded-full bg-emerald-500 px-4 py-2 text-sm font-semibold text-emerald-950 transition hover:bg-emerald-400">{{ __('Sign in') }}</button>
        </form>
    </div>
@endsection
