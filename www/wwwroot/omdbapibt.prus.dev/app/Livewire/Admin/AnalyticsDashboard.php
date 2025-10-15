<?php

namespace App\Livewire\Admin;

use App\Services\Admin\SubscriptionAnalyticsService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class AnalyticsDashboard extends Component
{
    public int $months = 6;

    /**
     * @var array<string, mixed>
     */
    public array $monthlyMetrics = [];

    /**
     * @var array<string, mixed>
     */
    public array $totals = [];

    public function mount(SubscriptionAnalyticsService $analytics): void
    {
        [$this->monthlyMetrics, $this->totals] = $analytics->getAnalytics($this->months);

        $this->dispatch('analytics-data-updated', chart: $this->chartPayload());
    }

    public function refreshMetrics(SubscriptionAnalyticsService $analytics): void
    {
        [$this->monthlyMetrics, $this->totals] = $analytics->refresh($this->months);

        $this->dispatch('analytics-data-updated', chart: $this->chartPayload());
    }

    private function chartPayload(): array
    {
        return [
            'labels' => $this->monthlyMetrics['labels'] ?? [],
            'newSubscriptions' => $this->monthlyMetrics['new_subscriptions'] ?? [],
            'churnedSubscriptions' => $this->monthlyMetrics['churned_subscriptions'] ?? [],
            'revenue' => $this->monthlyMetrics['revenue'] ?? [],
            'activeUsers' => $this->monthlyMetrics['active_users'] ?? [],
        ];
    }

    public function render()
    {
        return view('livewire.admin.analytics-dashboard', [
            'chartPayload' => $this->chartPayload(),
        ]);
    }
}
