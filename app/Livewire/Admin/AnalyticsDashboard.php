<?php

namespace App\Livewire\Admin;

use App\Services\Subscriptions\SubscriptionAnalyticsService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AnalyticsDashboard extends Component
{
    public array $totals = [];

    public array $plans = [];

    public array $trend = [];

    public array $catalog = [];

    public array $charts = [];

    public string $lastUpdated = '';

    public string $currency = 'USD';

    public function mount(SubscriptionAnalyticsService $analytics): void
    {
        if (! Auth::check() || ! Auth::user()->isAdmin()) {
            abort(403);
        }

        $this->loadMetrics($analytics);
    }

    public function refreshMetrics(SubscriptionAnalyticsService $analytics): void
    {
        $this->loadMetrics($analytics, true);
    }

    public function render(): View
    {
        return view('livewire.admin.analytics-dashboard');
    }

    protected function loadMetrics(SubscriptionAnalyticsService $analytics, bool $forceRefresh = false): void
    {
        $metrics = $analytics->getMetrics($forceRefresh);

        $this->totals = $metrics['totals'] ?? [];
        $this->plans = $metrics['plans'] ?? [];
        $this->trend = $metrics['trend'] ?? [];
        $this->catalog = $metrics['catalog'] ?? [];
        $this->currency = $metrics['currency'] ?? strtoupper(config('cashier.currency', 'usd'));
        $this->lastUpdated = $metrics['generated_at'] ?? now()->toDateTimeString();

        $this->charts = $this->chartPayload();

        $this->dispatch('subscription-metrics-updated', charts: $this->charts);
    }

    protected function chartPayload(): array
    {
        $planLabels = array_map(static fn (array $plan): string => $plan['name'], $this->plans);
        $planSeries = array_map(static fn (array $plan): int => (int) $plan['subscribers'], $this->plans);

        $trendCategories = array_map(static fn (array $point): string => $point['label'], $this->trend);
        $trendSignups = array_map(static fn (array $point): int => (int) $point['signups'], $this->trend);
        $trendRevenue = array_map(static fn (array $point): float => (float) $point['revenue'], $this->trend);

        return [
            'plans' => [
                'labels' => $planLabels,
                'series' => $planSeries,
            ],
            'trend' => [
                'categories' => $trendCategories,
                'signups' => $trendSignups,
                'revenue' => $trendRevenue,
            ],
            'currency' => $this->currency,
        ];
    }
}
