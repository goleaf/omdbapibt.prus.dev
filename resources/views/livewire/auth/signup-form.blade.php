<div class="mx-auto max-w-xl space-y-6 rounded-3xl border border-slate-800/60 bg-slate-900/70 p-8">
    @if (session()->has('status'))
        <div class="rounded-2xl border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit.prevent="submit" class="space-y-5">
        <div>
            <label for="name" class="text-sm font-semibold text-slate-200">{{ __('Name') }}</label>
            <input
                id="name"
                type="text"
                wire:model.live="name"
                class="mt-2 w-full rounded-2xl border border-slate-800 bg-slate-950 px-4 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none"
                placeholder="{{ __('Jane Doe') }}"
                autocomplete="name"
            >
            @error('name')
                <p class="mt-2 text-xs text-rose-300">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="text-sm font-semibold text-slate-200">{{ __('Email address') }}</label>
            <input
                id="email"
                type="email"
                wire:model.live="email"
                class="mt-2 w-full rounded-2xl border border-slate-800 bg-slate-950 px-4 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none"
                placeholder="{{ __('you@example.com') }}"
                autocomplete="email"
            >
            @error('email')
                <p class="mt-2 text-xs text-rose-300">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="text-sm font-semibold text-slate-200">{{ __('Password') }}</label>
            <input
                id="password"
                type="password"
                wire:model.live="password"
                class="mt-2 w-full rounded-2xl border border-slate-800 bg-slate-950 px-4 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none"
                placeholder="••••••••"
                autocomplete="new-password"
            >
            @error('password')
                <p class="mt-2 text-xs text-rose-300">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="text-sm font-semibold text-slate-200">{{ __('Confirm password') }}</label>
            <input
                id="password_confirmation"
                type="password"
                wire:model.live="password_confirmation"
                class="mt-2 w-full rounded-2xl border border-slate-800 bg-slate-950 px-4 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none"
                placeholder="••••••••"
                autocomplete="new-password"
            >
            @error('password_confirmation')
                <p class="mt-2 text-xs text-rose-300">{{ $message }}</p>
            @enderror
        </div>

        <div class="space-y-3 text-xs text-slate-300">
            <div class="flex items-center justify-between gap-3">
                <span>
                    {{ __('Already have an account?') }}
                    <a href="{{ route('login', ['locale' => app()->getLocale()]) }}" class="text-emerald-300 hover:text-emerald-200">
                        {{ __('Sign in') }}
                    </a>
                </span>
                <label class="flex items-start gap-2 text-left">
                    <input
                        type="checkbox"
                        wire:model.live="terms"
                        class="mt-0.5 size-4 rounded border-slate-700 bg-slate-900 text-emerald-400 focus:ring-emerald-400"
                    >
                    <span>{{ __('I agree to the terms of service.') }}</span>
                </label>
            </div>
            @error('terms')
                <p class="text-xs text-rose-300">{{ $message }}</p>
            @enderror
        </div>

        <button
            type="submit"
            class="w-full rounded-full bg-emerald-500 px-4 py-2 text-sm font-semibold text-emerald-950 transition hover:bg-emerald-400"
            wire:loading.attr="disabled"
            wire:target="submit"
        >
            <span wire:loading.class="hidden" wire:target="submit">{{ __('Create account') }}</span>
            <span wire:loading wire:target="submit" class="hidden text-emerald-950">{{ __('Creating...') }}</span>
        </button>
    </form>
</div>
