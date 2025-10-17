<div class="mx-auto max-w-5xl">
    <div class="grid gap-10 lg:grid-cols-[1.05fr_0.95fr]">
        <section class="rounded-3xl border border-emerald-400/25 bg-gradient-to-br from-emerald-950/60 via-slate-950/70 to-slate-900/60 p-10 shadow-[0_30px_60px_-34px_rgba(16,185,129,0.65)]">
            <h2 class="text-2xl font-semibold text-emerald-200">{{ __('Launch faster with a collaborative catalog hub') }}</h2>
            <p class="mt-3 text-sm text-slate-300">{{ __('Invite your team, orchestrate parser drops, and keep licensing teams aligned from a single workspace.') }}</p>

            <dl class="mt-8 space-y-6 text-sm text-slate-200">
                <div class="flex gap-4">
                    <div class="mt-0.5 flex h-8 w-8 items-center justify-center rounded-full bg-emerald-500/15 text-emerald-300">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M7 7v10M17 7v10M4 7h16M4 17h16M5 7l2-4h10l2 4" />
                        </svg>
                    </div>
                    <div>
                        <dt class="font-semibold text-slate-100">{{ __('Audience-ready data pipelines') }}</dt>
                        <dd class="mt-1 text-xs text-slate-300">{{ __('Blend OMDb, TMDb, and proprietary datasets with governance layers that keep downstream apps in sync.') }}</dd>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="mt-0.5 flex h-8 w-8 items-center justify-center rounded-full bg-emerald-500/15 text-emerald-300">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                            <circle cx="8.5" cy="7" r="4" />
                            <path d="M20 8v6" />
                            <path d="M23 11h-6" />
                        </svg>
                    </div>
                    <div>
                        <dt class="font-semibold text-slate-100">{{ __('Invite-only private screenings') }}</dt>
                        <dd class="mt-1 text-xs text-slate-300">{{ __('Host early previews, capture sentiment, and turn insights into action before public launch.') }}</dd>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="mt-0.5 flex h-8 w-8 items-center justify-center rounded-full bg-emerald-500/15 text-emerald-300">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M20 7h-9M20 12h-9M20 17h-9M7 7H4M7 12H4M7 17H4" />
                        </svg>
                    </div>
                    <div>
                        <dt class="font-semibold text-slate-100">{{ __('Granular permissions & audit trails') }}</dt>
                        <dd class="mt-1 text-xs text-slate-300">{{ __('Control who ships edits, automate reviews, and satisfy compliance across every slate update.') }}</dd>
                    </div>
                </div>
            </dl>

            <p class="mt-10 text-xs uppercase tracking-wide text-emerald-300/80">{{ __('Trusted by boutique studios and streamers shaping tomorrow’s release slate.') }}</p>
        </section>

        <section class="rounded-3xl border border-slate-800/60 bg-slate-950/80 p-10 shadow-[0_20px_42px_-28px_rgba(16,185,129,0.7)] backdrop-blur">
            <form wire:submit.prevent="register" class="space-y-6">
                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label for="signup-name" class="text-sm font-semibold text-slate-200">{{ __('Name') }}</label>
                        <input
                            id="signup-name"
                            type="text"
                            wire:model.live="name"
                            class="mt-2 w-full rounded-2xl border border-slate-800 bg-slate-950 px-4 py-2 text-sm text-slate-100 transition focus:border-emerald-400 focus:outline-none @error('name') border-rose-500/70 focus:border-rose-400 @enderror"
                            placeholder="{{ __('Jane Doe') }}"
                            autocomplete="name"
                        >
                        @error('name')
                            <p class="mt-2 text-xs font-semibold text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="signup-email" class="text-sm font-semibold text-slate-200">{{ __('Email address') }}</label>
                        <input
                            id="signup-email"
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
                </div>

                <div>
                    <label for="signup-password" class="text-sm font-semibold text-slate-200">{{ __('Password') }}</label>
                    <div class="relative mt-2">
                        <input
                            id="signup-password"
                            type="password"
                            wire:model.live="password"
                            class="peer w-full rounded-2xl border border-slate-800 bg-slate-950 px-4 py-2 pr-12 text-sm text-slate-100 transition focus:border-emerald-400 focus:outline-none @error('password') border-rose-500/70 focus:border-rose-400 @enderror"
                            placeholder="••••••••"
                            autocomplete="new-password"
                        >
                        <button
                            type="button"
                            class="absolute inset-y-0 right-2.5 flex items-center rounded-full px-2 text-slate-400 transition hover:text-emerald-300"
                            data-password-toggle
                            data-password-target="signup-password"
                            aria-controls="signup-password"
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

                <div class="space-y-3 rounded-2xl border border-slate-800/70 bg-slate-950/60 p-5">
                    <p class="text-sm font-semibold text-slate-100">{{ __('Agreement checklist') }}</p>
                    <label class="flex items-start gap-3 text-xs text-slate-300">
                        <input
                            type="checkbox"
                            wire:model.live="agreementsAccepted"
                            value="1"
                            class="mt-1.5 h-4 w-4 flex-shrink-0 rounded border-slate-700 bg-slate-900 text-emerald-400 focus:ring-emerald-500"
                            required
                        >
                        <span>
                            {{ __('I have reviewed the distribution agreements and legal terms that govern my workspace.') }}
                            <a href="{{ localized_route('agreements') }}" class="font-semibold text-emerald-300 transition hover:text-emerald-200" target="_blank" rel="noopener">{{ __('View agreements') }}</a>
                        </span>
                    </label>
                    @error('agreementsAccepted')
                        <p class="text-xs font-semibold text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    type="submit"
                    class="relative w-full rounded-full bg-emerald-500 px-4 py-2 text-sm font-semibold text-emerald-950 shadow-[0_12px_32px_-14px_rgba(16,185,129,0.82)] transition hover:bg-emerald-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-300"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove>{{ __('Create account') }}</span>
                    <span wire:loading class="flex items-center justify-center gap-2 text-emerald-950/90">
                        <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M21 12a9 9 0 1 1-6.219-8.56" />
                        </svg>
                        {{ __('Creating account...') }}
                    </span>
                </button>

                <p class="text-center text-xs text-slate-400">
                    {{ __('Already have an account?') }}
                    <a href="{{ localized_route('login') }}" class="font-semibold text-emerald-300 transition hover:text-emerald-200">{{ __('Sign in') }}</a>
                </p>
            </form>
        </section>
    </div>
</div>
