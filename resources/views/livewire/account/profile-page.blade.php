<section class="space-y-8">
    <div class="rounded-3xl border border-slate-800 bg-slate-900/70 p-6">
        <h2 class="text-lg font-semibold text-slate-100">{{ __('account.profile.sections.preferences.title') }}</h2>
        <p class="mt-1 text-sm text-slate-400">{{ __('account.profile.sections.preferences.description') }}</p>

        <dl class="mt-6 grid gap-3 md:grid-cols-2">
            @forelse ($preferences as $preference)
                <div class="flex items-center justify-between gap-4 rounded-2xl border border-slate-800 bg-slate-950/80 px-4 py-3 text-sm">
                    <dt class="text-slate-400">{{ $preference['label'] }}</dt>
                    <dd class="font-medium text-slate-100">{{ $preference['value'] }}</dd>
                </div>
            @empty
                <p class="text-sm text-slate-400">{{ __('account.profile.sections.preferences.empty') }}</p>
            @endforelse
        </dl>
    </div>

    <div class="grid gap-8 lg:grid-cols-2">
        <div class="rounded-3xl border border-slate-800 bg-slate-900/70 p-6">
            <h2 class="text-lg font-semibold text-slate-100">{{ __('account.profile.sections.favorites.title') }}</h2>
            <p class="mt-1 text-sm text-slate-400">{{ __('account.profile.sections.favorites.description') }}</p>

            <dl class="mt-6 space-y-3">
                @forelse ($favorites as $favorite)
                    <div class="flex items-center justify-between gap-4 rounded-2xl border border-slate-800 bg-slate-950/80 px-4 py-3 text-sm">
                        <dt class="text-slate-400">{{ $favorite['label'] }}</dt>
                        <dd class="font-medium text-slate-100">{{ $favorite['value'] }}</dd>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">{{ __('account.profile.sections.favorites.empty') }}</p>
                @endforelse
            </dl>
        </div>

        <div class="rounded-3xl border border-slate-800 bg-slate-900/70 p-6">
            <h2 class="text-lg font-semibold text-slate-100">{{ __('account.profile.sections.personal.title') }}</h2>
            <p class="mt-1 text-sm text-slate-400">{{ __('account.profile.sections.personal.description') }}</p>

            <dl class="mt-6 space-y-3">
                @forelse ($personalInformation as $entry)
                    <div class="rounded-2xl border border-slate-800 bg-slate-950/80 px-4 py-3 text-sm">
                        <dt class="text-xs uppercase tracking-wide text-slate-400">{{ $entry['label'] }}</dt>
                        <dd class="mt-1 font-medium text-slate-100">{{ $entry['value'] }}</dd>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">{{ __('account.profile.sections.personal.empty') }}</p>
                @endforelse
            </dl>
        </div>
    </div>

    <div class="rounded-3xl border border-slate-800 bg-slate-900/70 p-6">
        <h2 class="text-lg font-semibold text-slate-100">{{ __('account.profile.sections.social.title') }}</h2>
        <p class="mt-1 text-sm text-slate-400">{{ __('account.profile.sections.social.description') }}</p>

        <dl class="mt-6 grid gap-3 md:grid-cols-2">
            @forelse ($socialLinks as $link)
                <div class="rounded-2xl border border-slate-800 bg-slate-950/80 px-4 py-3 text-sm">
                    <dt class="text-xs uppercase tracking-wide text-slate-400">{{ $link['label'] }}</dt>
                    <dd class="mt-1 font-medium text-emerald-300">
                        @if (\Illuminate\Support\Str::startsWith($link['value'], ['http://', 'https://']))
                            <a href="{{ $link['value'] }}" class="hover:text-emerald-200" target="_blank" rel="noopener noreferrer">{{ $link['value'] }}</a>
                        @else
                            {{ $link['value'] }}
                        @endif
                    </dd>
                </div>
            @empty
                <p class="text-sm text-slate-400">{{ __('account.profile.sections.social.empty') }}</p>
            @endforelse
        </dl>
    </div>
</section>
