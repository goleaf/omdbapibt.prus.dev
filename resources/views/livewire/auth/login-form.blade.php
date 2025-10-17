<div class="mx-auto max-w-5xl">
    <div class="grid gap-10 lg:grid-cols-[1.05fr_0.95fr]">
        <section class="rounded-3xl border border-emerald-500/20 bg-gradient-to-br from-slate-900/70 via-slate-900/60 to-emerald-900/30 p-10 shadow-[0_30px_60px_-30px_rgba(16,185,129,0.55)]">
            <h2 class="text-2xl font-semibold text-emerald-200">{{ __('Return to your curated universe') }}</h2>
            <p class="mt-3 text-sm text-slate-300">{{ __('Pick up your watchlists, continue collaborative sessions, and stay in sync with real-time parser drops.') }}</p>

            <ul class="mt-8 space-y-4 text-sm text-slate-200">
                <li class="flex items-start gap-3">
                    <span class="mt-1 inline-flex h-6 w-6 items-center justify-center rounded-full bg-emerald-500/15 text-emerald-300">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 0 1 0 1.42l-6.657 6.657a1 1 0 0 1-1.414 0L3.296 8.73a1 1 0 1 1 1.414-1.414l4.033 4.032 5.95-5.95a1 1 0 0 1 1.414 0Z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    <div>
                        <p class="font-semibold text-slate-100">{{ __('Secure sessions') }}</p>
                        <p class="mt-1 text-xs text-slate-300">{{ __('We protect every login with adaptive rate limiting and rotating session tokens.') }}</p>
                    </div>
                </li>
                <li class="flex items-start gap-3">
                    <span class="mt-1 inline-flex h-6 w-6 items-center justify-center rounded-full bg-emerald-500/15 text-emerald-300">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 0 1 0 1.42l-6.657 6.657a1 1 0 0 1-1.414 0L3.296 8.73a1 1 0 1 1 1.414-1.414l4.033 4.032 5.95-5.95a1 1 0 0 1 1.414 0Z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    <div>
                        <p class="font-semibold text-slate-100">{{ __('Unified workspace') }}</p>
                        <p class="mt-1 text-xs text-slate-300">{{ __('Collaborate across devices with synchronized notes, rails, and editorial drafts.') }}</p>
                    </div>
                </li>
                <li class="flex items-start gap-3">
                    <span class="mt-1 inline-flex h-6 w-6 items-center justify-center rounded-full bg-emerald-500/15 text-emerald-300">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 0 1 0 1.42l-6.657 6.657a1 1 0 0 1-1.414 0L3.296 8.73a1 1 0 1 1 1.414-1.414l4.033 4.032 5.95-5.95a1 1 0 0 1 1.414 0Z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    <div>
                        <p class="font-semibold text-slate-100">{{ __('Real-time intelligence') }}</p>
                        <p class="mt-1 text-xs text-slate-300">{{ __('Parser activity, trending cohorts, and launch alerts stream directly into your dashboard.') }}</p>
                    </div>
                </li>
            </ul>

            <p class="mt-10 text-xs uppercase tracking-wide text-emerald-300/80">{{ __('Need a hand? Visit our support center or message the concierge team anytime.') }}</p>
        </section>

        <section class="rounded-3xl border border-slate-800/60 bg-slate-950/80 p-10 shadow-[0_20px_40px_-24px_rgba(15,118,110,0.65)] backdrop-blur">
            <form wire:submit.prevent="login" class="space-y-6">
                <div>
                    <label for="login-email" class="text-sm font-semibold text-slate-200">{{ __('Email address') }}</label>
                    <input
                        id="login-email"
                        type="email"
                        wire:model.live="email"
                        class="mt-2 w-full rounded-2xl border border-slate-800 bg-slate-950 px-4 py-2 text-sm text-slate-100 transition focus:border-emerald-400 focus:outline-none @error('email') border-rose-500/70 focus:border-rose-400 @enderror"
                        placeholder="{{ __('you@example.com') }}"
                        autocomplete="email"
                        inputmode="email"
                    >
                    @error('email')
                        <p class="mt-2 text-xs font-semibold text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="login-password" class="text-sm font-semibold text-slate-200">{{ __('Password') }}</label>
                    <div class="relative mt-2">
                        <input
                            id="login-password"
                            type="password"
                            wire:model.live="password"
                            class="peer w-full rounded-2xl border border-slate-800 bg-slate-950 px-4 py-2 pr-12 text-sm text-slate-100 transition focus:border-emerald-400 focus:outline-none @error('password') border-rose-500/70 focus:border-rose-400 @enderror"
                            placeholder="••••••••"
                            autocomplete="current-password"
                        >
                        <button
                            type="button"
                            class="absolute inset-y-0 right-2.5 flex items-center rounded-full px-2 text-slate-400 transition hover:text-emerald-300"
                            data-password-toggle
                            data-password-target="login-password"
                            aria-controls="login-password"
                            aria-label="{{ __('Toggle password visibility') }}"
                        >
                            <span data-password-icon="show" class="flex items-center">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                            </span>
                            <span data-password-icon="hide" class="hidden items-center">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="m3 3 18 18" />
                                    <path d="M10.58 10.58A2 2 0 0 0 12 14a2 2 0 0 0 1.42-.58" />
                                    <path d="M17.94 17.94C16.12 19.25 14.12 20 12 20 5 20 1 12 1 12a21.6 21.6 0 0 1 5.06-6.94" />
                                    <path d="M9.88 5.1A9.12 9.12 0 0 1 12 5c7 0 11 7 11 7a21.67 21.67 0 0 1-3.23 4.95" />
                                </svg>
                            </span>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-2 text-xs font-semibold text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col gap-3 text-xs text-slate-300 sm:flex-row sm:items-center sm:justify-between">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" wire:model.live="remember" class="h-4 w-4 rounded border-slate-700 bg-slate-900 text-emerald-400 focus:ring-emerald-500">
                        <span>{{ __('Remember me') }}</span>
                    </label>
                    <a href="{{ localized_route('password.request') }}" class="text-emerald-300 transition hover:text-emerald-200">{{ __('Forgot password?') }}</a>
                </div>

                <button
                    type="submit"
                    class="relative w-full rounded-full bg-emerald-500 px-4 py-2 text-sm font-semibold text-emerald-950 shadow-[0_12px_30px_-12px_rgba(16,185,129,0.8)] transition hover:bg-emerald-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-300"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove>{{ __('Sign in') }}</span>
                    <span wire:loading class="flex items-center justify-center gap-2 text-emerald-950/90">
                        <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M21 12a9 9 0 1 1-6.219-8.56" />
                        </svg>
                        {{ __('Signing in...') }}
                    </span>
                </button>
            </form>

            <p class="mt-8 text-center text-xs text-slate-400">
                {{ __('New to OMDb Stream?') }}
                <a href="{{ localized_route('register') }}" class="font-semibold text-emerald-300 transition hover:text-emerald-200">{{ __('Create an account') }}</a>
            </p>
        </section>
    </div>
</div>
