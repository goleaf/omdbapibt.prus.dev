<div class="mx-auto max-w-4xl">
    <div class="grid gap-10 lg:grid-cols-[0.95fr_1.05fr]">
        <section class="rounded-3xl border border-slate-800/70 bg-gradient-to-br from-slate-950/70 via-slate-900/60 to-emerald-950/40 p-10 shadow-[0_28px_56px_-34px_rgba(16,185,129,0.6)]">
            <h2 class="text-2xl font-semibold text-emerald-200">{{ __('Reset access in minutes') }}</h2>
            <p class="mt-3 text-sm text-slate-300">{{ __('We will send a one-time secure link that lets you create a fresh password while keeping your workspace protected.') }}</p>

            <ol class="mt-8 space-y-6 text-sm text-slate-200">
                <li class="flex gap-4">
                    <div class="mt-0.5 flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-emerald-500/15 text-emerald-300">1</div>
                    <div>
                        <h3 class="font-semibold text-slate-100">{{ __('Submit your account email') }}</h3>
                        <p class="mt-1 text-xs text-slate-300">{{ __('Use the address you registered with or the one tied to your organization’s SSO bridge.') }}</p>
                    </div>
                </li>
                <li class="flex gap-4">
                    <div class="mt-0.5 flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-emerald-500/15 text-emerald-300">2</div>
                    <div>
                        <h3 class="font-semibold text-slate-100">{{ __('Check for the secure link') }}</h3>
                        <p class="mt-1 text-xs text-slate-300">{{ __('The message arrives within a minute. Add OMDb Stream to your safe senders list to prevent delays.') }}</p>
                    </div>
                </li>
                <li class="flex gap-4">
                    <div class="mt-0.5 flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-emerald-500/15 text-emerald-300">3</div>
                    <div>
                        <h3 class="font-semibold text-slate-100">{{ __('Create a new password') }}</h3>
                        <p class="mt-1 text-xs text-slate-300">{{ __('Follow the instructions to choose a strong password. The link expires automatically to keep things secure.') }}</p>
                    </div>
                </li>
            </ol>

            <p class="mt-10 text-xs uppercase tracking-wide text-emerald-300/80">{{ __('Need help from a person? Visit support or message us for a manual reset.') }}</p>
        </section>

        <section class="rounded-3xl border border-slate-800/60 bg-slate-950/80 p-10 shadow-[0_20px_42px_-28px_rgba(16,185,129,0.7)] backdrop-blur">
            <form wire:submit.prevent="sendResetLink" class="space-y-6">
                <div>
                    <label for="reset-email" class="text-sm font-semibold text-slate-200">{{ __('Email address') }}</label>
                    <input
                        id="reset-email"
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

                @if ($statusMessage)
                    <div class="rounded-2xl border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                        {{ $statusMessage }}
                    </div>
                @endif

                <button
                    type="submit"
                    class="relative w-full rounded-full bg-emerald-500 px-4 py-2 text-sm font-semibold text-emerald-950 shadow-[0_12px_30px_-12px_rgba(16,185,129,0.8)] transition hover:bg-emerald-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-300"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove>{{ __('Email reset link') }}</span>
                    <span wire:loading class="flex items-center justify-center gap-2 text-emerald-950/90">
                        <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M21 12a9 9 0 1 1-6.219-8.56" />
                        </svg>
                        {{ __('Sending...') }}
                    </span>
                </button>

                <p class="text-center text-xs text-slate-400">
                    {{ __('Remembered your password?') }}
                    <a href="{{ localized_route('login') }}" class="font-semibold text-emerald-300 transition hover:text-emerald-200">{{ __('Return to sign in') }}</a>
                </p>
            </form>
        </section>
    </div>
</div>
