@php
    use Illuminate\Support\Str;
@endphp

<div class="space-y-12">
    <section class="grid gap-10 lg:grid-cols-[320px,1fr]">
        <div class="space-y-6">
            <div class="overflow-hidden rounded-3xl border border-slate-800 bg-slate-900/80 shadow-lg shadow-slate-950/40">
                <img
                    src="{{ $person->profile_image_url }}"
                    alt="{{ $person->name }} profile photo"
                    class="w-full object-cover"
                >
            </div>

            <div class="space-y-4 rounded-3xl border border-slate-800 bg-slate-900/60 p-6">
                <div class="space-y-1">
                    <p class="text-xs uppercase tracking-widest text-emerald-400">{{ __('Person') }}</p>
                    <h1 class="text-3xl font-semibold text-slate-50">{{ $person->name }}</h1>
                    @if ($person->known_for_department)
                        <p class="text-sm text-slate-300">{{ $person->known_for_department }}</p>
                    @endif
                </div>

                <dl class="space-y-3 text-sm text-slate-300">
                    @forelse ($personalDetails as $label => $value)
                        <div class="flex items-start justify-between gap-3" wire:key="detail-{{ Str::slug($label) }}">
                            <dt class="font-medium text-slate-200">{{ $label }}</dt>
                            <dd class="text-right text-slate-300">{{ $value }}</dd>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400">{{ __('No personal details available.') }}</p>
                    @endforelse
                </dl>
            </div>
        </div>

        <div class="space-y-8">
            <section class="space-y-4">
                <header class="flex items-center justify-between">
                    <h2 class="text-2xl font-semibold text-slate-50">{{ __('Biography') }}</h2>
                    <span class="text-xs uppercase tracking-widest text-slate-400">{{ __('Translations') }}</span>
                </header>

                <div class="grid gap-4 md:grid-cols-2">
                    @forelse ($biographyTranslations as $translation)
                        <article
                            class="space-y-3 rounded-3xl border border-slate-800 bg-slate-900/60 p-6"
                            wire:key="bio-{{ $translation['locale'] }}"
                        >
                            <header class="flex items-center justify-between text-xs uppercase tracking-widest">
                                <span class="rounded-full bg-slate-800 px-3 py-1 font-semibold text-emerald-300">
                                    {{ $translation['label'] }}
                                </span>
                                <span class="text-slate-500">{{ __('Locale') }}</span>
                            </header>
                            <p class="text-sm leading-relaxed text-slate-200">{{ $translation['biography'] }}</p>
                        </article>
                    @empty
                        <p class="text-sm text-slate-400">{{ __('No biography translations available.') }}</p>
                    @endforelse
                </div>
            </section>

            <section class="space-y-4">
                <header class="flex items-center justify-between">
                    <h2 class="text-2xl font-semibold text-slate-50">{{ __('Movie Credits') }}</h2>
                    <span class="text-xs uppercase tracking-widest text-slate-400">{{ __('Grouped by role') }}</span>
                </header>

                @forelse ($movieCreditsByRole as $group)
                    <div
                        class="space-y-4 rounded-3xl border border-slate-800 bg-slate-900/60 p-6"
                        wire:key="movie-role-{{ $group['role_key'] }}"
                    >
                        <h3 class="text-lg font-semibold text-emerald-300">{{ $group['role_label'] }}</h3>
                        <ul class="space-y-3">
                            @foreach ($group['credits'] as $credit)
                                <li
                                    class="flex gap-4 rounded-2xl border border-slate-800 bg-slate-950/70 p-4"
                                    wire:key="movie-credit-{{ $group['role_key'] }}-{{ Str::slug($credit['title']) }}-{{ $loop->index }}"
                                >
                                    <div class="h-20 w-14 overflow-hidden rounded-xl border border-slate-800 bg-slate-900/80">
                                        <img src="{{ $credit['poster'] }}" alt="{{ $credit['title'] }} poster" class="h-full w-full object-cover">
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-base font-semibold text-slate-100">{{ $credit['title'] }}</p>
                                        <div class="mt-1 flex flex-wrap items-center gap-3 text-xs uppercase tracking-wide text-slate-400">
                                            @if ($credit['year'])
                                                <span>{{ $credit['year'] }}</span>
                                            @endif
                                            @if ($credit['character'])
                                                <span>{{ __('as') }} {{ $credit['character'] }}</span>
                                            @endif
                                            @if ($credit['job'])
                                                <span>{{ $credit['job'] }}</span>
                                            @endif
                                            @if ($credit['department'])
                                                <span>{{ $credit['department'] }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">{{ __('No movie credits available.') }}</p>
                @endforelse
            </section>

            <section class="space-y-4">
                <header class="flex items-center justify-between">
                    <h2 class="text-2xl font-semibold text-slate-50">{{ __('TV Credits') }}</h2>
                    <span class="text-xs uppercase tracking-widest text-slate-400">{{ __('Grouped by role') }}</span>
                </header>

                @forelse ($tvCreditsByRole as $group)
                    <div
                        class="space-y-4 rounded-3xl border border-slate-800 bg-slate-900/60 p-6"
                        wire:key="tv-role-{{ $group['role_key'] }}"
                    >
                        <h3 class="text-lg font-semibold text-emerald-300">{{ $group['role_label'] }}</h3>
                        <ul class="space-y-3">
                            @foreach ($group['credits'] as $credit)
                                <li
                                    class="flex gap-4 rounded-2xl border border-slate-800 bg-slate-950/70 p-4"
                                    wire:key="tv-credit-{{ $group['role_key'] }}-{{ Str::slug($credit['title']) }}-{{ $loop->index }}"
                                >
                                    <div class="h-20 w-14 overflow-hidden rounded-xl border border-slate-800 bg-slate-900/80">
                                        <img src="{{ $credit['poster'] }}" alt="{{ $credit['title'] }} poster" class="h-full w-full object-cover">
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-base font-semibold text-slate-100">{{ $credit['title'] }}</p>
                                        <div class="mt-1 flex flex-wrap items-center gap-3 text-xs uppercase tracking-wide text-slate-400">
                                            @if ($credit['year'])
                                                <span>{{ $credit['year'] }}</span>
                                            @endif
                                            @if ($credit['character'])
                                                <span>{{ __('as') }} {{ $credit['character'] }}</span>
                                            @endif
                                            @if ($credit['job'])
                                                <span>{{ $credit['job'] }}</span>
                                            @endif
                                            @if ($credit['department'])
                                                <span>{{ $credit['department'] }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">{{ __('No TV credits available.') }}</p>
                @endforelse
            </section>
        </div>
    </section>
</div>
