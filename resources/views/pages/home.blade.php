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

<div class="space-y-20">
    <section class="relative overflow-hidden rounded-[2.75rem] border border-slate-800/60 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-900 p-8 shadow-2xl ring-1 ring-emerald-500/15 sm:p-12">
        <div class="pointer-events-none absolute -top-32 right-20 h-80 w-80 rounded-full bg-emerald-500/20 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-10 left-1/3 h-72 w-72 rounded-full bg-emerald-400/10 blur-3xl"></div>
        <div class="pointer-events-none absolute top-1/2 left-0 hidden h-px w-full bg-gradient-to-r from-emerald-400/0 via-emerald-400/40 to-transparent lg:block"></div>

        <div class="relative grid gap-12 lg:grid-cols-[minmax(0,1fr),minmax(320px,400px)] lg:items-center">
            <div class="space-y-8">
                <div class="space-y-4">
                    <flux:badge variant="solid" color="emerald">Flux + Livewire orchestration</flux:badge>
                    <h1 class="text-4xl font-bold text-white sm:text-5xl lg:text-6xl">Command streaming releases from every screen</h1>
                    <p class="text-base text-slate-300 sm:text-lg">
                        Curate slates, audit rights, and launch cross-network programming in a single responsive hub. Optimized controls keep tablet editors and desktop strategists aligned without context switching.
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
                                <flux:icon icon="arrow-path" class="size-4" />
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
                                <flux:icon icon="clock" class="size-4" />
                                Autoschedule
                            </span>
                            <span class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1 text-[11px] uppercase tracking-[0.25em] text-slate-200">
                                <flux:icon icon="adjustments-horizontal" class="size-4" />
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
                                <flux:icon icon="{{ $step['icon'] }}" class="size-6" />
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
                            <flux:icon icon="plus" class="size-5" />
                        </span>
                    </summary>
                    <p class="mt-4 text-sm leading-relaxed text-slate-300">{{ $faq['answer'] }}</p>
                </details>
            @endforeach
        </div>
    </x-landing.section>
</div>
