<div
    class="space-y-10"
    wire:poll.60s="refreshMetrics"
    x-data="subscriptionAnalyticsDashboard(@js($charts))"
    x-init="init()"
>
    <header class="flex flex-col gap-4 border-b border-slate-800 pb-6 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-semibold text-slate-50">Subscription Analytics</h1>
            <p class="mt-1 text-sm text-slate-400">
                Monitor revenue performance, plan adoption, and subscription health in real time.
            </p>
        </div>
        <div class="flex flex-col items-start gap-2 text-right text-sm text-slate-400 sm:items-end">
            <p class="font-semibold text-slate-200">Last updated</p>
            <p>{{ $lastUpdated ?: '—' }}</p>
            <div class="flex gap-2">
                <button
                    type="button"
                    wire:click="refreshMetrics"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 rounded-full border border-emerald-500/70 px-4 py-2 text-sm font-medium text-emerald-300 transition hover:border-emerald-300 hover:text-emerald-200 disabled:cursor-not-allowed disabled:border-slate-700 disabled:text-slate-500"
                >
                    <svg
                        class="h-4 w-4"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.5"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M16.862 4.487a9.168 9.168 0 011.677 2.384m1.012 3.853a9.17 9.17 0 01-.93 3.857m-2.674 3.287a9.167 9.167 0 01-2.386 1.663m-3.857.986a9.168 9.168 0 01-3.857-.986m-3.287-2.674a9.167 9.167 0 01-1.663-2.386m-.986-3.857a9.168 9.168 0 01.986-3.857m2.674-3.287a9.167 9.167 0 012.386-1.663"
                        />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l3 3" />
                    </svg>
                    <span wire:loading.remove>Refresh now</span>
                    <span wire:loading>Refreshing…</span>
                </button>
                <span class="rounded-full border border-slate-800 px-3 py-1 text-xs uppercase tracking-wide text-slate-500">
                    Currency: {{ strtoupper($currency) }}
                </span>
            </div>
        </div>
    </header>

    <section class="grid gap-4 md:grid-cols-3 xl:grid-cols-6">
        @php
            $formatCurrency = static function ($amount, string $currency): string {
                $code = strtoupper($currency);

                return $code.' '.number_format((float) $amount, 2);
            };
        @endphp

        @foreach ([
            ['label' => 'Active subscriptions', 'value' => number_format((int) ($totals['active_subscriptions'] ?? 0))],
            ['label' => 'Active customers', 'value' => number_format((int) ($totals['active_customers'] ?? 0))],
            ['label' => 'Trialing', 'value' => number_format((int) ($totals['trialing_subscriptions'] ?? 0))],
            ['label' => 'Churn (30d)', 'value' => number_format((int) ($totals['churned_last_30_days'] ?? 0))],
            ['label' => 'Monthly recurring revenue', 'value' => $formatCurrency($totals['mrr'] ?? 0, $currency)],
            ['label' => 'Annual recurring revenue', 'value' => $formatCurrency($totals['arr'] ?? 0, $currency)],
        ] as $stat)
            <div class="rounded-2xl border border-slate-800 bg-slate-950/40 p-4 shadow-lg shadow-emerald-500/5">
                <p class="text-xs uppercase tracking-wide text-slate-500">{{ $stat['label'] }}</p>
                <p class="mt-2 text-2xl font-semibold text-slate-100">{{ $stat['value'] }}</p>
            </div>
        @endforeach
    </section>

    <section class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="rounded-3xl border border-slate-800 bg-slate-900/60 p-6 shadow-xl shadow-emerald-500/10">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-100">Revenue &amp; signups trend</h2>
                    <p class="text-xs uppercase tracking-wide text-slate-500">Last 6 months</p>
                </div>
                <div class="mt-4" wire:ignore x-ref="trendChart"></div>
            </div>

            <div class="rounded-3xl border border-slate-800 bg-slate-900/60 p-6 shadow-xl shadow-indigo-500/10">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-100">Plan catalog</h2>
                    <p class="text-xs uppercase tracking-wide text-slate-500">Configured plans</p>
                </div>
                <div class="mt-5 grid gap-4 md:grid-cols-2">
                    @forelse ($catalog as $plan)
                        <article class="rounded-2xl border border-slate-800/70 bg-slate-950/60 p-4">
                            <h3 class="text-base font-semibold text-emerald-300">{{ $plan['name'] }}</h3>
                            <p class="mt-1 text-sm text-slate-400">
                                {{ strtoupper($plan['currency'] ?? $currency) }}
                                {{ number_format(($plan['amount'] ?? 0) / 100, 2) }}
                                / {{ ($plan['interval_count'] ?? 1) > 1 ? ($plan['interval_count'].' '.$plan['interval'].'s') : ($plan['interval'] ?? 'month') }}
                            </p>
                            <dl class="mt-3 space-y-1 text-xs text-slate-500">
                                <div class="flex items-center justify-between">
                                    <dt>Stripe price</dt>
                                    <dd class="font-mono text-slate-300">{{ $plan['price_id'] ?: '—' }}</dd>
                                </div>
                                <div class="flex items-center justify-between">
                                    <dt>Slug</dt>
                                    <dd class="font-mono text-slate-300">{{ $plan['slug'] ?? '—' }}</dd>
                                </div>
                            </dl>
                            @if (! empty($plan['features']))
                                <ul class="mt-3 space-y-1 text-xs text-slate-400">
                                    @foreach ($plan['features'] as $feature)
                                        <li class="flex items-start gap-2">
                                            <span class="mt-0.5 inline-flex h-1.5 w-1.5 flex-shrink-0 rounded-full bg-emerald-400"></span>
                                            <span>{{ $feature }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </article>
                    @empty
                        <p class="text-sm text-slate-400">No subscription plans configured. Update <code class="rounded bg-slate-800 px-1.5 py-0.5">config/subscriptions.php</code> to add catalog entries.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <aside class="space-y-6">
            <div class="rounded-3xl border border-slate-800 bg-slate-900/60 p-6 shadow-xl shadow-emerald-500/10">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-100">Plan mix</h2>
                    <p class="text-xs uppercase tracking-wide text-slate-500">Active subscribers</p>
                </div>
                @if (empty($plans))
                    <p class="mt-6 text-sm text-slate-400">No active subscriptions found.</p>
                @else
                    <div class="mt-4" wire:ignore x-ref="plansChart"></div>
                    <ul class="mt-6 space-y-2 text-sm text-slate-300">
                        @foreach ($plans as $plan)
                            <li class="flex items-center justify-between rounded-2xl border border-slate-800/80 bg-slate-950/50 px-4 py-3">
                                <div>
                                    <p class="font-semibold text-slate-100">{{ $plan['name'] }}</p>
                                    <p class="text-xs text-slate-500">{{ number_format($plan['subscribers']) }} subscribers • {{ $formatCurrency($plan['monthly_price'] ?? 0, $plan['currency'] ?? $currency) }}/mo</p>
                                </div>
                                <span class="text-sm font-semibold text-emerald-300">{{ $formatCurrency($plan['mrr'] ?? 0, $plan['currency'] ?? $currency) }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="rounded-3xl border border-slate-800 bg-slate-900/60 p-6 shadow-xl shadow-indigo-500/10">
                <h2 class="text-lg font-semibold text-slate-100">Refresh cadence</h2>
                <p class="mt-2 text-sm text-slate-400">
                    Metrics are cached for {{ config('subscriptions.cache.metrics_ttl', config('cache_ttls.analytics.subscription_metrics')) }} seconds.
                    Use the refresh control to regenerate the aggregated results immediately when needed.
                </p>
            </div>
        </aside>
    </section>
</div>

@once
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script>
            function subscriptionAnalyticsDashboard(initialCharts) {
                return {
                    charts: initialCharts || { plans: { labels: [], series: [] }, trend: { categories: [], revenue: [], signups: [] }, currency: 'USD' },
                    trendChart: null,
                    plansChart: null,
                    init() {
                        this.renderCharts();
                        Livewire.on('subscription-metrics-updated', ({ charts }) => {
                            this.charts = charts;
                            this.renderCharts();
                        });
                    },
                    renderCharts() {
                        this.renderTrendChart();
                        this.renderPlansChart();
                    },
                    renderTrendChart() {
                        const el = this.$refs.trendChart;

                        if (! el) {
                            return;
                        }

                        const currency = (this.charts.currency || 'USD').toUpperCase();
                        const options = {
                            chart: {
                                type: 'line',
                                height: 320,
                                toolbar: { show: false },
                                fontFamily: 'Inter, ui-sans-serif',
                                foreColor: '#94a3b8',
                                animations: { easing: 'easeinout', speed: 600 },
                            },
                            stroke: {
                                curve: 'smooth',
                                width: [3, 0],
                            },
                            fill: {
                                type: ['gradient', 'solid'],
                                gradient: {
                                    shade: 'dark',
                                    type: 'vertical',
                                    shadeIntensity: 0.5,
                                    gradientToColors: ['#14b8a6'],
                                    inverseColors: false,
                                    opacityFrom: 0.9,
                                    opacityTo: 0.3,
                                    stops: [0, 80, 100],
                                },
                            },
                            colors: ['#34d399', '#6366f1'],
                            grid: {
                                borderColor: '#1e293b',
                                strokeDashArray: 4,
                            },
                            series: [
                                {
                                    name: `Revenue (${currency})`,
                                    type: 'area',
                                    data: this.charts.trend.revenue,
                                },
                                {
                                    name: 'New signups',
                                    type: 'column',
                                    data: this.charts.trend.signups,
                                },
                            ],
                            xaxis: {
                                categories: this.charts.trend.categories,
                                labels: { rotate: -45 },
                                axisBorder: { color: '#1e293b' },
                                axisTicks: { color: '#1e293b' },
                            },
                            yaxis: [
                                {
                                    title: { text: `Revenue (${currency})`, style: { color: '#34d399' } },
                                    labels: {
                                        formatter(value) {
                                            return new Intl.NumberFormat('en-US', { style: 'currency', currency }).format(value || 0);
                                        },
                                    },
                                },
                                {
                                    opposite: true,
                                    title: { text: 'New signups', style: { color: '#6366f1' } },
                                    labels: {
                                        formatter(value) {
                                            return Math.round(value || 0).toString();
                                        },
                                    },
                                },
                            ],
                            dataLabels: { enabled: false },
                            legend: {
                                position: 'top',
                                horizontalAlign: 'left',
                                labels: { colors: '#cbd5f5' },
                            },
                            tooltip: {
                                shared: true,
                                intersect: false,
                                theme: 'dark',
                                y: {
                                    formatter(value, { seriesIndex }) {
                                        if (seriesIndex === 0) {
                                            return new Intl.NumberFormat('en-US', { style: 'currency', currency }).format(value || 0);
                                        }

                                        return `${Math.round(value || 0)} signups`;
                                    },
                                },
                            },
                        };

                        if (this.trendChart) {
                            this.trendChart.updateOptions(options);
                        } else {
                            this.trendChart = new ApexCharts(el, options);
                            this.trendChart.render();
                        }
                    },
                    renderPlansChart() {
                        const el = this.$refs.plansChart;

                        if (! el) {
                            return;
                        }

                        if (! this.charts.plans.series.length) {
                            if (this.plansChart) {
                                this.plansChart.destroy();
                                this.plansChart = null;
                            }

                            el.innerHTML = '<p class="text-sm text-slate-400">No active subscription data.</p>';

                            return;
                        }

                        const options = {
                            chart: {
                                type: 'donut',
                                height: 320,
                                fontFamily: 'Inter, ui-sans-serif',
                                foreColor: '#94a3b8',
                            },
                            labels: this.charts.plans.labels,
                            series: this.charts.plans.series,
                            colors: ['#10b981', '#6366f1', '#f59e0b', '#f87171', '#8b5cf6'],
                            legend: {
                                position: 'bottom',
                                labels: { colors: '#cbd5f5' },
                            },
                            dataLabels: {
                                formatter(value, { seriesIndex }) {
                                    const count = (this.w.globals.series[seriesIndex] || 0).toLocaleString();

                                    return `${count} • ${value.toFixed(1)}%`;
                                },
                            },
                            stroke: { colors: ['#0f172a'] },
                        };

                        if (this.plansChart) {
                            this.plansChart.updateOptions(options);
                        } else {
                            this.plansChart = new ApexCharts(el, options);
                            this.plansChart.render();
                        }
                    },
                };
            }
        </script>
    @endpush
@endonce
