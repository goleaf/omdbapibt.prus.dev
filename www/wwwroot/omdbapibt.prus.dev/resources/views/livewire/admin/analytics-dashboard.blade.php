@php
    $generatedAt = $totals['generated_at'] ?? null;
    if ($generatedAt instanceof \Carbon\CarbonInterface) {
        $generatedCarbon = $generatedAt;
    } elseif ($generatedAt) {
        $generatedCarbon = \Carbon\Carbon::parse($generatedAt);
    } else {
        $generatedCarbon = null;
    }

    $formatCurrency = static fn ($value) => '$' . number_format((float) $value, 2);
    $windowLength = count($monthlyMetrics['labels'] ?? []);
    $windowLabel = $windowLength === 1 ? 'month' : $windowLength . ' months';
    $currentLabel = $windowLength > 0 ? $monthlyMetrics['labels'][$windowLength - 1] : 'current period';
@endphp

<div class="relative space-y-8">
    <div
        class="absolute inset-0 z-10 hidden items-center justify-center rounded-2xl bg-white/70 backdrop-blur"
        wire:loading.flex
        wire:target="refreshMetrics"
    >
        <div class="inline-flex items-center gap-3 rounded-full bg-blue-600/90 px-4 py-2 text-sm font-semibold text-white shadow">
            <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
            </svg>
            Refreshing analytics…
        </div>
    </div>

    <div class="flex flex-col gap-6 md:flex-row md:items-start md:justify-between">
        <div class="space-y-2">
            <h1 class="text-3xl font-semibold text-slate-900">Subscription analytics</h1>
            <p class="text-sm text-slate-500">
                Aggregated metrics sourced from Stripe Cashier subscriptions and the local subscription plan catalog.
            </p>
            @if ($generatedCarbon)
                <p class="text-xs text-slate-400">
                    Cached {{ $generatedCarbon->diffForHumans() }} ({{ $generatedCarbon->toDayDateTimeString() }})
                </p>
            @endif
        </div>
        <div class="flex items-center gap-3">
            <button
                wire:click="refreshMetrics"
                wire:loading.attr="disabled"
                class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow transition hover:bg-blue-500 disabled:cursor-not-allowed disabled:bg-blue-300"
            >
                <span wire:loading.remove wire:target="refreshMetrics">Refresh data</span>
                <span class="inline-flex items-center gap-2" wire:loading.flex wire:target="refreshMetrics">
                    <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                    Updating…
                </span>
            </button>
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl bg-white p-6 shadow">
            <p class="text-sm font-medium text-slate-500">Active subscribers</p>
            <p class="mt-3 text-3xl font-semibold text-slate-900">
                {{ number_format((int) ($totals['active_users'] ?? 0)) }}
            </p>
            <p class="mt-1 text-xs text-slate-400">Unique users with an active subscription</p>
        </div>

        <div class="rounded-2xl bg-white p-6 shadow">
            <p class="text-sm font-medium text-slate-500">Monthly recurring revenue</p>
            <p class="mt-3 text-3xl font-semibold text-slate-900">
                {{ $formatCurrency($totals['monthly_recurring_revenue'] ?? 0) }}
            </p>
            <p class="mt-1 text-xs text-slate-400">Latest cycle: {{ $currentLabel }}</p>
            <p class="mt-1 text-xs text-slate-400">Revenue across {{ $windowLabel }}: {{ $formatCurrency($totals['total_revenue'] ?? 0) }}</p>
        </div>

        <div class="rounded-2xl bg-white p-6 shadow">
            <p class="text-sm font-medium text-slate-500">New subscriptions</p>
            <p class="mt-3 text-3xl font-semibold text-slate-900">
                {{ number_format((int) ($totals['new_subscriptions'] ?? 0)) }}
            </p>
            <p class="mt-1 text-xs text-slate-400">New sign-ups captured this month</p>
        </div>

        <div class="rounded-2xl bg-white p-6 shadow">
            <p class="text-sm font-medium text-slate-500">Churn rate</p>
            <p class="mt-3 text-3xl font-semibold text-slate-900">
                {{ number_format((float) ($totals['churn_rate'] ?? 0), 2) }}%
            </p>
            <p class="mt-1 text-xs text-slate-400">Churned subscriptions: {{ number_format((int) ($totals['churned_subscriptions'] ?? 0)) }}</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl bg-white p-6 shadow">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Subscription movements</h2>
                    <p class="text-sm text-slate-500">New subscriptions vs churned accounts</p>
                </div>
            </div>
            <div class="mt-6" wire:ignore>
                <div id="subscriptions-trend" class="h-72"></div>
            </div>
        </div>

        <div class="rounded-2xl bg-white p-6 shadow">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Revenue trend</h2>
                    <p class="text-sm text-slate-500">Monthly recurring revenue from active plans</p>
                </div>
            </div>
            <div class="mt-6" wire:ignore>
                <div id="revenue-trend" class="h-72"></div>
            </div>
        </div>
    </div>

    <div class="rounded-2xl bg-white p-6 shadow">
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Active users trend</h2>
                <p class="text-sm text-slate-500">Unique subscribers with access each month</p>
            </div>
        </div>
        <div class="mt-6" wire:ignore>
            <div id="active-users-trend" class="h-72"></div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        window.__analyticsInitialPayload = @json($chartPayload);
    </script>
@endpush

@push('scripts')
    @once
        <script>
            document.addEventListener('livewire:initialized', () => {
                let subscriptionChart;
                let revenueChart;
                let activeUsersChart;

                const renderOrUpdate = (chart, element, options) => {
                    if (! element) {
                        return chart;
                    }

                    if (! chart) {
                        chart = new ApexCharts(element, options);
                        chart.render();
                    } else {
                        chart.updateOptions(options);
                    }

                    return chart;
                };

                const buildSubscriptionOptions = (payload) => ({
                    chart: { type: 'bar', height: 320, toolbar: { show: false } },
                    series: [
                        { name: 'New subscriptions', data: payload.newSubscriptions },
                        { name: 'Churned subscriptions', data: payload.churnedSubscriptions },
                    ],
                    xaxis: { categories: payload.labels },
                    dataLabels: { enabled: false },
                    plotOptions: { bar: { columnWidth: '45%', borderRadius: 6 } },
                    colors: ['#2563eb', '#f97316'],
                    grid: { strokeDashArray: 4 },
                });

                const buildRevenueOptions = (payload) => ({
                    chart: { type: 'line', height: 320, toolbar: { show: false } },
                    series: [
                        { name: 'Revenue', data: payload.revenue },
                    ],
                    xaxis: { categories: payload.labels },
                    stroke: { curve: 'smooth', width: 3 },
                    dataLabels: { enabled: false },
                    colors: ['#16a34a'],
                    grid: { strokeDashArray: 4 },
                    tooltip: {
                        y: {
                            formatter: (value) => '$' + Number.parseFloat(value || 0).toFixed(2),
                        },
                    },
                    yaxis: {
                        labels: {
                            formatter: (value) => '$' + Number.parseFloat(value || 0).toFixed(2),
                        },
                    },
                });

                const buildActiveUsersOptions = (payload) => ({
                    chart: { type: 'area', height: 320, toolbar: { show: false } },
                    series: [
                        { name: 'Active users', data: payload.activeUsers },
                    ],
                    xaxis: { categories: payload.labels },
                    dataLabels: { enabled: false },
                    stroke: { curve: 'smooth', width: 2 },
                    fill: {
                        type: 'gradient',
                        gradient: { shadeIntensity: 0.5, opacityFrom: 0.4, opacityTo: 0.1, stops: [0, 90, 100] },
                    },
                    colors: ['#7c3aed'],
                    grid: { strokeDashArray: 4 },
                });

                const updateCharts = (payload) => {
                    if (typeof ApexCharts === 'undefined' || ! payload) {
                        return;
                    }

                    const normalized = {
                        labels: payload.labels ?? [],
                        newSubscriptions: payload.newSubscriptions ?? [],
                        churnedSubscriptions: payload.churnedSubscriptions ?? [],
                        revenue: (payload.revenue ?? []).map((value) => Number.parseFloat(value ?? 0)),
                        activeUsers: payload.activeUsers ?? [],
                    };

                    subscriptionChart = renderOrUpdate(
                        subscriptionChart,
                        document.querySelector('#subscriptions-trend'),
                        buildSubscriptionOptions(normalized)
                    );

                    revenueChart = renderOrUpdate(
                        revenueChart,
                        document.querySelector('#revenue-trend'),
                        buildRevenueOptions(normalized)
                    );

                    activeUsersChart = renderOrUpdate(
                        activeUsersChart,
                        document.querySelector('#active-users-trend'),
                        buildActiveUsersOptions(normalized)
                    );
                };

                if (window.__analyticsInitialPayload) {
                    updateCharts(window.__analyticsInitialPayload);
                }

                Livewire.on('analytics-data-updated', ({ chart }) => {
                    updateCharts(chart);
                });
            });
        </script>
    @endonce
@endpush
