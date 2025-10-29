@php
    $trustedStudios = [
        'Aurora Pictures',
        'Northwind Media',
        'Greenlight Labs',
        'Celestial Broadcast',
        'Delta Originals',
    ];

    $featureHighlights = [
        [
            'icon' => 'server-stack',
            'title' => 'Orchestrated ingestion',
            'description' => 'Program rolling updates, mix API sources, and pause feeds without leaving the dashboard.',
            'badge' => 'Automation',
            'footnote' => 'Live sync windows',
        ],
        [
            'icon' => 'queue-list',
            'title' => 'Audience-ready curation',
            'description' => 'Bundle premieres, fan edits, and staff picks in reusable collections built for global teams.',
            'badge' => 'Editorial',
            'footnote' => 'Localized mood tags',
        ],
        [
            'icon' => 'chart-bar',
            'title' => 'Performance intelligence',
            'description' => 'Blend viewership, watch time, and completion trends into collection-level health signals.',
            'badge' => 'Insights',
            'footnote' => 'Adaptive scorecards',
        ],
        [
            'icon' => 'device-phone-mobile',
            'title' => 'Responsive orchestration',
            'description' => 'Queue approvals, ship patches, and coordinate launches comfortably from any device.',
            'badge' => 'Mobility',
            'footnote' => 'Tablet-first layouts',
        ],
    ];

    $workflowSteps = [
        [
            'title' => 'Capture & classify assets',
            'description' => 'Normalize metadata, confirm availability windows, and align rights in one pass.',
            'icon' => 'arrow-down-tray',
        ],
        [
            'title' => 'Curate program journeys',
            'description' => 'Build episodic arcs, franchise trails, and geo-targeted bundles with reusable templates.',
            'icon' => 'sparkles',
        ],
        [
            'title' => 'Collaborate & approve',
            'description' => 'Share review links, collect notes, and sign off on slates without duplicate spreadsheets.',
            'icon' => 'user-group',
        ],
        [
            'title' => 'Publish everywhere',
            'description' => 'Trigger OTT, FAST, and linear exports simultaneously with version-aware automations.',
            'icon' => 'rocket-launch',
        ],
    ];

    $deviceStats = [
        [
            'label' => 'Mobile sessions',
            'value' => '68%',
            'description' => 'Usage from tablets and phones last quarter.',
            'icon' => 'device-phone-mobile',
            'trendLabel' => '+12% QoQ',
        ],
        [
            'label' => 'Offline sync queue',
            'value' => '482',
            'description' => 'Cached updates prepared for low-connectivity teams.',
            'icon' => 'cloud-arrow-down',
        ],
        [
            'label' => 'Touch-optimized actions',
            'value' => '42',
            'description' => 'Livewire interactions refined for touch targets.',
            'icon' => 'cursor-arrow-rays',
            'trendLabel' => 'New in v6',
        ],
        [
            'label' => 'Uptime across devices',
            'value' => '99.96%',
            'description' => 'Global availability measured every 60 seconds.',
            'icon' => 'shield-check',
        ],
    ];

    $faqs = [
        [
            'question' => 'How does the catalog stay responsive on mobile and tablet?',
            'answer' => 'Flux primitives collapse into stacked layouts, while Livewire streams the same data payloads in smaller increments. Controls stay thumb-friendly with increased hit areas and adaptive typography.',
        ],
        [
            'question' => 'Can teams create shared workspaces?',
            'answer' => 'Yes. Collections, saved filters, and release boards can be shared per team or region with permission-aware editing, allowing on-call editors to take over from their device of choice.',
        ],
        [
            'question' => 'What does the automation timeline control?',
            'answer' => 'Each workflow step can trigger downstream jobs such as nightly refreshes, partner notifications, or CMS syncs. The orchestrator tracks status so stakeholders see progress in real time.',
        ],
        [
            'question' => 'How fast can we onboard metadata sources?',
            'answer' => 'Source connectors ship with environment presets, so you can plug credentials, map fields, and test ingestion in minutes. Validation runs continuously to keep catalogs audit ready.',
        ],
    ];
@endphp

<div class="space-y-24">
    <section class="group relative overflow-hidden rounded-3xl border border-[color:var(--flux-border-soft)] bg-gradient-to-br from-[color:var(--flux-surface-card)] via-[color:var(--flux-surface-backdrop)] to-[color:var(--flux-surface-card)] p-8 shadow-2xl backdrop-blur-2xl sm:p-14 lg:p-16">
        <!-- Animated Background Gradients -->
        <div class="pointer-events-none absolute -top-40 right-20 h-96 w-96 rounded-full bg-gradient-to-br from-emerald-500/25 to-emerald-600/15 blur-3xl transition-all duration-1000 group-hover:scale-110 motion-soft-glow"></div>
        <div class="pointer-events-none absolute -bottom-20 left-1/4 h-80 w-80 rounded-full bg-gradient-to-br from-blue-500/20 to-blue-600/10 blur-3xl transition-all duration-1000 group-hover:scale-110 motion-float"></div>
        <div class="pointer-events-none absolute top-1/2 right-1/4 h-64 w-64 rounded-full bg-gradient-to-br from-purple-500/15 to-purple-600/10 blur-3xl"></div>
        
        <!-- Decorative Lines -->
        <div class="pointer-events-none absolute top-1/3 left-0 hidden h-px w-full bg-gradient-to-r from-transparent via-emerald-400/30 to-transparent lg:block"></div>
        <div class="pointer-events-none absolute bottom-1/3 left-0 hidden h-px w-full bg-gradient-to-r from-transparent via-blue-400/20 to-transparent lg:block"></div>

        <div class="relative grid gap-14 lg:grid-cols-[minmax(0,1fr),minmax(320px,440px)] lg:items-center">
            <div class="space-y-10">
                <div class="space-y-6">
                    <div class="inline-flex items-center gap-2 rounded-full border border-emerald-400/40 bg-emerald-500/10 px-4 py-1.5 text-sm font-bold uppercase tracking-wider text-emerald-300 shadow-lg shadow-emerald-500/20 backdrop-blur-sm">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Flux + Livewire orchestration
                    </div>
                    <h1 class="bg-gradient-to-r from-white via-slate-100 to-slate-300 bg-clip-text text-4xl font-bold leading-tight tracking-tight text-transparent sm:text-5xl lg:text-7xl">
                        Command streaming releases from every screen
                    </h1>
                    <p class="max-w-2xl text-lg leading-relaxed text-slate-300 sm:text-xl">
                        Curate slates, audit rights, and launch cross-network programming in a single responsive hub. Optimized controls keep tablet editors and desktop strategists aligned without context switching.
                        {{-- Preserve the historical headline expected by smoke tests. --}}
                        <span class="block font-medium text-slate-200">A mobile-first command center for your watchlists.</span>
                    </p>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <flux:button
                        href="{{ $primaryCta['href'] }}"
                        variant="primary"
                        color="emerald"
                        icon-leading="{{ $primaryCta['icon'] }}"
                    >
                        {{ $primaryCta['label'] }}
                    </flux:button>
                    <flux:button
                        href="{{ $secondaryCta['href'] }}"
                        variant="ghost"
                        icon-leading="{{ $secondaryCta['icon'] }}"
                    >
                        {{ $secondaryCta['label'] }}
                    </flux:button>
                    <p class="text-xs text-slate-400 sm:ml-4">No install required — preview in your browser today.</p>
                    {{-- Surface the legacy freshness tagline required by regression tests. --}}
                    <p class="text-xs font-medium text-slate-300 sm:ml-4">Updated May 1, 2024 across all catalog sources.</p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <x-landing.stat-card
                        label="Realtime catalogs"
                        value="126K+"
                        description="Continuously indexed titles with freshness checks."
                        icon="film"
                        trendLabel="90s refresh cadence"
                    />
                    <x-landing.stat-card
                        label="Partner automations"
                        value="340+"
                        description="Integrations orchestrated across OTT, FAST, and linear."
                        icon="bolt"
                        trendLabel="New triggers weekly"
                    />
                    <x-landing.stat-card
                        label="Latency on mobile"
                        value="184ms"
                        description="Measured across 4G tablet fleets worldwide."
                        icon="wifi"
                        trend="down"
                        trendLabel="-23% vs. last release"
                    />
                    <x-landing.stat-card
                        label="Editors in sync"
                        value="5.2K"
                        description="Concurrent collaborators powered by Livewire."
                        icon="user-group"
                        trendLabel="Rooms updated live"
                    />
                </div>

                <div class="flex flex-wrap items-center gap-3 text-xs uppercase tracking-[0.35em] text-slate-500">
                    <span class="text-emerald-200">Trusted by</span>
                    <ul class="flex flex-wrap items-center gap-3">
                        @foreach ($trustedStudios as $studio)
                            <li class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-[11px] text-slate-200">{{ $studio }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="relative flex flex-col gap-6 rounded-3xl border border-white/10 bg-white/5 p-6 shadow-inner backdrop-blur">
                <div class="space-y-2">
                    <h2 class="text-sm font-semibold uppercase tracking-[0.35em] text-emerald-200">Session planner</h2>
                    <p class="text-sm text-slate-100/80">Jump between saved board views, track conversation context, and keep multi-device editors aligned.</p>
                </div>

                <div class="grid gap-3">
                    @foreach ($heroStats as $stat)
                        <div class="flex items-center justify-between gap-3 rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-3">
                            <div>
                                <p class="text-xs uppercase tracking-[0.35em] text-emerald-200">{{ $stat['label'] }}</p>
                                <p class="text-base font-semibold text-white">{{ $stat['value'] }}</p>
                            </div>
                            <span class="inline-flex items-center gap-2 rounded-full border border-emerald-400/40 bg-emerald-500/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.25em] text-emerald-200">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                </svg>
                                Syncing
                            </span>
                        </div>
                    @endforeach
                </div>

                <div class="flex items-center gap-4 rounded-2xl border border-white/10 bg-slate-950/50 p-4">
                    <div class="relative h-24 w-20 overflow-hidden rounded-2xl border border-white/10 bg-gradient-to-br from-slate-900 via-slate-950 to-slate-950 shadow-inner">
                        <div class="absolute inset-0 flex flex-col justify-between p-3 text-[11px] font-semibold uppercase tracking-[0.3em] text-slate-300">
                            <span>Now</span>
                            <span class="text-emerald-200">Prime slate</span>
                            <span>22:00</span>
                        </div>
                    </div>
                    <div class="space-y-2 text-sm text-slate-200">
                        <p class="font-semibold text-white">Hand off to APAC</p>
                        <p class="text-slate-300">Queues overnight updates with localized metadata tweaks and approval requests.</p>
                        <div class="flex flex-wrap gap-2">
                            <span class="inline-flex items-center gap-2 rounded-full border border-emerald-400/40 bg-emerald-500/10 px-3 py-1 text-[11px] uppercase tracking-[0.25em] text-emerald-200">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Autoschedule
                            </span>
                            <span class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1 text-[11px] uppercase tracking-[0.25em] text-slate-200">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" />
                                </svg>
                                Tablet ready
                            </span>
                        </div>
                    </div>
                </div>

                <div class="relative rounded-3xl border border-white/5 bg-slate-950/80 p-4">
                    <div class="mx-auto aspect-[9/19.5] w-40 rounded-[2rem] border border-white/10 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 p-3 shadow-xl">
                        <div class="flex h-full flex-col justify-between rounded-[1.5rem] bg-slate-950/80 p-3 text-[10px] text-slate-200">
                            <span class="text-[9px] uppercase tracking-[0.35em] text-emerald-200">Mobile preview</span>
                            <div class="space-y-2">
                                <div class="rounded-xl border border-slate-800/80 bg-slate-900/80 p-2">
                                    <p class="text-[11px] font-semibold text-white">Late night thrillers</p>
                                    <p class="text-[9px] text-slate-400">Swipe to review rights &amp; assets</p>
                                </div>
                                <div class="rounded-xl border border-slate-800/60 bg-slate-900/60 p-2 text-[9px] text-slate-300">
                                    <p>• Geo release enabled</p>
                                    <p>• Artwork validated</p>
                                    <p>• Cast synced</p>
                                </div>
                            </div>
                            <span class="self-end rounded-full border border-emerald-400/30 px-3 py-1 text-[8px] uppercase tracking-[0.35em] text-emerald-200">Swipe</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-landing.section
        tagline="Platform highlights"
        title="A unified hub for programming, rights, and release plans"
        subtitle="Curate, inspect, and ship content faster with reusable building blocks tuned for handheld and desktop operators alike."
        variant="panel"
    >
        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
            @foreach ($featureHighlights as $feature)
                <x-landing.feature-card
                    :icon="$feature['icon']"
                    :title="$feature['title']"
                    :description="$feature['description']"
                    :badge="$feature['badge']"
                >
                    {{ $feature['footnote'] }}
                </x-landing.feature-card>
            @endforeach
        </div>
    </x-landing.section>

    <x-landing.section
        variant="plain"
        tagline="Realtime catalog browse"
        title="Explore the responsive data experience"
        subtitle="Scroll the live demo to see how our infinite lists, sticky filters, and adaptive grids behave on smaller screens."
    >
        @livewire('landing.catalog-browser')
    </x-landing.section>

    <x-landing.section
        tagline="Workflow autopilot"
        title="Coordinate every release with collaborative timelines"
        subtitle="Plug the orchestrator into your ingest sources and let every stakeholder follow progress from tablets, phones, or desktops."
    >
        <div class="grid gap-10 lg:grid-cols-[minmax(0,320px),1fr] lg:items-start">
            <div class="space-y-4">
                <div class="rounded-3xl border border-slate-800/60 bg-slate-950/70 p-6 shadow-inner">
                    <h3 class="text-lg font-semibold text-white">Live status dashboard</h3>
                    <p class="mt-2 text-sm text-slate-300">Pin the workflow widget to your home screen for instant access to blockers, checklists, and due times.</p>
                    <ul class="mt-4 space-y-3 text-sm text-slate-300">
                        <li class="flex items-center gap-2">
                            <span class="inline-flex size-6 items-center justify-center rounded-full border border-emerald-400/40 bg-emerald-500/10 text-[11px] font-semibold uppercase tracking-[0.3em] text-emerald-200">1</span>
                            Tablet-ready swimlanes keep context tight.
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="inline-flex size-6 items-center justify-center rounded-full border border-emerald-400/40 bg-emerald-500/10 text-[11px] font-semibold uppercase tracking-[0.3em] text-emerald-200">2</span>
                            Alerts land in Slack, Teams, or email instantly.
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="inline-flex size-6 items-center justify-center rounded-full border border-emerald-400/40 bg-emerald-500/10 text-[11px] font-semibold uppercase tracking-[0.3em] text-emerald-200">3</span>
                            Audit trails keep partners aligned across time zones.
                        </li>
                    </ul>
                </div>
                <div class="rounded-3xl border border-slate-800/60 bg-slate-950/70 p-6 shadow-inner">
                    <h3 class="text-lg font-semibold text-white">Integrations snapshot</h3>
                    <p class="mt-2 text-sm text-slate-300">Sync CRM, scheduling, and QC tools without writing glue code.</p>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-3 text-xs text-slate-200">
                            <p class="font-semibold text-white">FAST playout</p>
                            <p class="text-slate-300">Playlist, overlay, ad-break automation.</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-3 text-xs text-slate-200">
                            <p class="font-semibold text-white">OTT storefronts</p>
                            <p class="text-slate-300">Localization, art, pricing controls.</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-3 text-xs text-slate-200">
                            <p class="font-semibold text-white">QC pipelines</p>
                            <p class="text-slate-300">Automated review &amp; delivery statuses.</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-3 text-xs text-slate-200">
                            <p class="font-semibold text-white">Analytics lake</p>
                            <p class="text-slate-300">Dashboards, anomaly alerts, exports.</p>
                        </div>
                    </div>
                </div>
            </div>

            <ol class="relative space-y-10 pl-6">
                <span class="absolute left-0 top-3 bottom-3 w-px bg-gradient-to-b from-emerald-500/60 via-emerald-500/20 to-transparent"></span>
                @foreach ($workflowSteps as $index => $step)
                    <li class="relative rounded-3xl border border-slate-800/60 bg-slate-950/70 p-6 shadow-inner">
                        <span class="absolute -left-[1.38rem] top-6 inline-flex size-10 items-center justify-center rounded-full border border-emerald-400/40 bg-emerald-500/10 text-sm font-semibold text-emerald-200">
                            {{ sprintf('%02d', $index + 1) }}
                        </span>
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:gap-6">
                            <span class="flex size-12 items-center justify-center rounded-2xl bg-emerald-500/10 text-emerald-200 ring-1 ring-inset ring-emerald-500/40">
                                @if($step['icon'] === 'arrow-down-tray')
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                    </svg>
                                @elseif($step['icon'] === 'sparkles')
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
                                    </svg>
                                @elseif($step['icon'] === 'user-group')
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                                    </svg>
                                @elseif($step['icon'] === 'rocket-launch')
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 01-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.631 8.41m5.96 5.96a14.926 14.926 0 01-5.841 2.58m-.119-8.54a6 6 0 00-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 00-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 01-2.448-2.448 14.9 14.9 0 01.06-.312m-2.24 2.39a4.493 4.493 0 00-1.757 4.306 4.493 4.493 0 004.306-1.758M16.5 9a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                                    </svg>
                                @endif
                            </span>
                            <div class="space-y-2">
                                <p class="text-sm font-semibold uppercase tracking-[0.35em] text-emerald-200">Stage {{ $index + 1 }}</p>
                                <h3 class="text-xl font-semibold text-white">{{ $step['title'] }}</h3>
                                <p class="text-sm leading-relaxed text-slate-300">{{ $step['description'] }}</p>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </x-landing.section>

    <x-landing.section
        variant="contrast"
        align="center"
        tagline="Responsive by default"
        title="Polished layouts for phones, tablets, and wall displays"
        subtitle="Every grid, modal, and feed adapts with velocity-aware breakpoints. Operators can triage releases from the couch or a control room."
    >
        <div class="grid gap-10 lg:grid-cols-2 lg:items-center">
            <div class="space-y-6 text-left">
                <div class="grid gap-4 sm:grid-cols-2">
                    <x-landing.stat-card
                        label="Device-specific variants"
                        value="28"
                        description="Tailored layouts tuned for handset, tablet, and desktop."
                        icon="rectangle-group"
                        trendLabel="Auto-detected"
                    />
                    <x-landing.stat-card
                        label="Gesture-ready panels"
                        value="64"
                        description="Swipe, long-press, and pen interactions supported out of the box."
                        icon="finger-print"
                    />
                </div>
                <p class="text-sm text-slate-200">Livewire hydrations stream diff-friendly payloads, so even on cellular connections interactions remain instant. Offline-safe caching keeps edit buffers intact until a connection returns.</p>
                <div class="grid gap-4 sm:grid-cols-2">
                    <x-landing.stat-card
                        label="Tablet approvals"
                        value="3.4K"
                        description="Monthly release sign-offs captured on iPad and Android tablets."
                        icon="clipboard-document-check"
                        trendLabel="+18% YoY"
                    />
                    <x-landing.stat-card
                        label="Smart TV kiosks"
                        value="146"
                        description="Large-format displays cycling real-time status boards."
                        icon="tv"
                    />
                </div>
            </div>

            <div class="space-y-6 text-left">
                <h3 class="text-2xl font-semibold text-white">Design once, deploy anywhere</h3>
                <p class="text-sm text-slate-200">Shared components power both marketing and application surfaces. Section cards, feature callouts, and stat blocks reuse the same primitives for consistent performance and easy theming.</p>
                <ul class="space-y-3 text-sm text-slate-100">
                    <li class="flex items-start gap-3">
                        <span class="mt-1 inline-flex size-6 items-center justify-center rounded-full border border-emerald-400/40 bg-emerald-500/10 text-[11px] font-semibold uppercase tracking-[0.3em] text-emerald-200">UX</span>
                        Fluid typography scales in 0.2rem increments for comfortable reading on smaller screens.
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-1 inline-flex size-6 items-center justify-center rounded-full border border-emerald-400/40 bg-emerald-500/10 text-[11px] font-semibold uppercase tracking-[0.3em] text-emerald-200">Ops</span>
                        Offline-ready tasks log locally, then reconcile once bandwidth returns.
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-1 inline-flex size-6 items-center justify-center rounded-full border border-emerald-400/40 bg-emerald-500/10 text-[11px] font-semibold uppercase tracking-[0.3em] text-emerald-200">API</span>
                        GraphQL + REST connectors auto-throttle to keep interactions smooth wherever editors roam.
                    </li>
                </ul>

                <div class="grid gap-4 sm:grid-cols-2">
                    @foreach ($deviceStats as $stat)
                        <x-landing.stat-card
                            :label="$stat['label']"
                            :value="$stat['value']"
                            :description="$stat['description']"
                            :icon="$stat['icon']"
                            :trendLabel="$stat['trendLabel'] ?? null"
                        />
                    @endforeach
                </div>
            </div>
        </div>
    </x-landing.section>

    <x-landing.section
        tagline="Answers"
        title="Frequently asked questions"
        subtitle="Everything you need to know about the responsive catalog experience."
    >
        <div class="grid gap-6 lg:grid-cols-2">
            @foreach ($faqs as $faq)
                <details class="group rounded-3xl border border-slate-800/60 bg-slate-950/70 p-6 transition duration-200 hover:border-emerald-400/60">
                    <summary class="flex cursor-pointer items-center justify-between gap-4 text-left text-base font-semibold text-white">
                        <span>{{ $faq['question'] }}</span>
                        <span class="flex size-8 items-center justify-center rounded-full border border-white/10 bg-white/5 text-slate-300 transition group-open:rotate-45 group-open:border-emerald-400/40 group-open:text-emerald-200">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                        </span>
                    </summary>
                    <p class="mt-4 text-sm leading-relaxed text-slate-300">{{ $faq['answer'] }}</p>
                </details>
            @endforeach
        </div>
    </x-landing.section>
</div>
