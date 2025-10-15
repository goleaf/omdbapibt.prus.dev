<div class="space-y-10">
    <div class="rounded-[2.5rem] border border-slate-800/60 bg-slate-900/70 p-8 shadow-xl">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-start">
            <div class="flex-1 space-y-4">
                <h1 class="text-4xl font-semibold text-white">{{ $movieModel->localizedTitle() }}</h1>
                <div class="flex flex-wrap items-center gap-3 text-sm text-slate-300">
                    <x-flux.rating-badge :score="$movieModel->vote_average ?? 'N/A'" label="Rating" />
                    <span class="rounded-full border border-slate-700/60 px-3 py-1 text-xs uppercase tracking-[0.35em]">{{ $movieModel->year }}</span>
                    <span class="rounded-full border border-slate-700/60 px-3 py-1 text-xs uppercase tracking-[0.35em]">{{ $movieModel->runtime }} min</span>
                    <span class="text-xs text-slate-400">{{ $movieModel->genres->pluck('name')->implode(', ') }}</span>
                </div>
                <p class="text-base text-slate-200">{{ $movieModel->tagline }}</p>
                <p class="text-sm leading-relaxed text-slate-300">{{ $movieModel->overview['en'] ?? $movieModel->plot }}</p>
            </div>
            <img src="{{ $posterUrl }}" alt="{{ $movieModel->localizedTitle() }} poster" class="w-56 rounded-3xl border border-slate-800/60 object-cover" />
        </div>
    </div>

    <div>
        <div class="flex flex-wrap gap-3">
            @foreach ($tabs as $tab => $label)
                <button
                    type="button"
                    wire:click="setTab('{{ $tab }}')"
                    @class([
                        'rounded-full border px-4 py-2 text-xs font-semibold uppercase tracking-[0.35em] transition',
                        'border-emerald-400/70 bg-emerald-500/10 text-emerald-100' => $activeTab === $tab,
                        'border-slate-700 text-slate-300 hover:border-emerald-400 hover:text-emerald-200' => $activeTab !== $tab,
                    ])
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    <div>
        @switch($activeTab)
            @case('overview')
                <x-flux.card heading="Synopsis">
                    <p class="text-sm text-slate-300">{{ $movieModel->overview['en'] ?? $movieModel->plot }}</p>
                </x-flux.card>
            @break

            @case('credits')
                <x-flux.card heading="Cast" subheading="Top billed talent">
                    <ul class="space-y-2 text-sm text-slate-300">
                        @foreach ($cast as $entry)
                            <li class="flex items-center justify-between">
                                <span>{{ $entry['name'] }}</span>
                                <span class="text-xs text-slate-400">{{ $entry['role'] ?? '—' }}</span>
                            </li>
                        @endforeach
                    </ul>
                </x-flux.card>
                <x-flux.card heading="Crew" subheading="Key contributors">
                    <ul class="space-y-2 text-sm text-slate-300">
                        @foreach ($crew as $entry)
                            <li class="flex items-center justify-between">
                                <span>{{ $entry['name'] }}</span>
                                <span class="text-xs text-slate-400">{{ $entry['role'] ?? '—' }}</span>
                            </li>
                        @endforeach
                    </ul>
                </x-flux.card>
            @break

            @case('streaming')
                <x-flux.card heading="Streaming availability">
                    <ul class="space-y-2 text-sm text-slate-300">
                        @forelse ($streaming as $link)
                            <li class="flex items-center justify-between">
                                <span>{{ $link['provider'] ?? 'Provider' }}</span>
                                <span class="text-xs text-slate-400">{{ $link['quality'] ?? 'HD' }}</span>
                            </li>
                        @empty
                            <li class="text-xs text-slate-400">No streaming links provided.</li>
                        @endforelse
                    </ul>
                </x-flux.card>
            @break

            @case('trailers')
                <x-flux.card heading="Trailers">
                    <div class="space-y-4">
                        @forelse ($trailers as $trailer)
                            <a href="{{ $trailer['url'] ?? '#' }}" target="_blank" class="flex items-center justify-between rounded-2xl border border-slate-800/60 bg-slate-900/60 px-4 py-3 text-sm text-emerald-200 transition hover:border-emerald-400/60 hover:text-emerald-100">
                                <span>{{ $trailer['title'] ?? 'Trailer' }}</span>
                                <span class="text-xs text-slate-400">Open</span>
                            </a>
                        @empty
                            <p class="text-xs text-slate-400">No trailers available.</p>
                        @endforelse
                    </div>
                </x-flux.card>
            @break

            @case('translations')
                <x-flux.card heading="Translations">
                    <ul class="space-y-2 text-sm text-slate-300">
                        @forelse ($translations['title'] ?? [] as $locale => $value)
                            <li class="flex items-center justify-between">
                                <span class="uppercase tracking-[0.35em]">{{ $locale }}</span>
                                <span>{{ $value }}</span>
                            </li>
                        @empty
                            <li class="text-xs text-slate-400">No translation metadata recorded.</li>
                        @endforelse
                    </ul>
                </x-flux.card>
            @break

            @case('reviews')
                <x-flux.card heading="Reviews">
                    <p class="text-sm text-slate-300">Review aggregation is coming soon.</p>
                </x-flux.card>
            @break
        @endswitch
    </div>
</div>
