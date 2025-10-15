<?php

namespace App\Services\Admin;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SubscriptionAnalyticsService
{
    private const CACHE_TTL_MINUTES = 15;

    /**
     * Stripe statuses that indicate an active subscription.
     *
     * @var array<int, string>
     */
    private const ACTIVE_STATUSES = [
        'trialing',
        'active',
        'past_due',
        'unpaid',
    ];

    /**
     * Retrieve cached subscription analytics.
     *
     * @return array{0: array<string, mixed>, 1: array<string, mixed>}
     */
    public function getAnalytics(int $months = 6): array
    {
        $months = max(1, $months);

        $monthly = Cache::remember(
            $this->monthlyCacheKey($months),
            now()->addMinutes(self::CACHE_TTL_MINUTES),
            fn () => $this->buildMonthlyMetrics($months)
        );

        $totals = Cache::remember(
            $this->totalsCacheKey($months),
            now()->addMinutes(self::CACHE_TTL_MINUTES),
            fn () => $this->buildSummary($monthly)
        );

        return [$monthly, $totals];
    }

    /**
     * Clear the cached analytics and recalculate them.
     *
     * @return array{0: array<string, mixed>, 1: array<string, mixed>}
     */
    public function refresh(int $months = 6): array
    {
        $months = max(1, $months);

        Cache::forget($this->monthlyCacheKey($months));
        Cache::forget($this->totalsCacheKey($months));

        return $this->getAnalytics($months);
    }

    /**
     * Build the monthly metrics dataset.
     *
     * @return array<string, mixed>
     */
    private function buildMonthlyMetrics(int $months): array
    {
        $periodEnd = Carbon::now()->endOfMonth();
        $periodStart = (clone $periodEnd)->startOfMonth()->subMonths($months - 1);

        $subscriptions = DB::table('subscriptions')
            ->select('id', 'user_id', 'stripe_status', 'created_at', 'ends_at')
            ->where('created_at', '<=', $periodEnd)
            ->where(function ($query) use ($periodStart) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', $periodStart);
            })
            ->get()
            ->map(function ($subscription) {
                $subscription->created_at = Carbon::parse($subscription->created_at);
                $subscription->ends_at = $subscription->ends_at ? Carbon::parse($subscription->ends_at) : null;

                return $subscription;
            });

        $subscriptionIds = $subscriptions->pluck('id');

        $revenuePerSubscription = DB::table('subscription_items')
            ->select('subscription_items.subscription_id', 'subscription_items.quantity', 'subscription_plans.amount')
            ->leftJoin('subscription_plans', 'subscription_plans.stripe_price_id', '=', 'subscription_items.stripe_price')
            ->whereIn('subscription_items.subscription_id', $subscriptionIds)
            ->get()
            ->groupBy('subscription_id')
            ->map(function (Collection $items) {
                return $items->sum(function ($item) {
                    $quantity = $item->quantity ?? 1;

                    if ($quantity < 1) {
                        $quantity = 1;
                    }

                    $amount = $item->amount ?? 0;

                    return (int) $amount * (int) $quantity;
                });
            });

        $labels = [];
        $newSubscriptions = [];
        $churnedSubscriptions = [];
        $revenue = [];
        $activeUsers = [];

        for ($i = 0; $i < $months; $i++) {
            $monthStart = (clone $periodStart)->addMonths($i);
            $monthEnd = (clone $monthStart)->endOfMonth();

            $labels[] = $monthStart->format('M Y');

            $newSubscriptions[] = $subscriptions
                ->filter(fn ($subscription) => $this->isActiveStatus($subscription->stripe_status)
                    && $subscription->created_at->between($monthStart, $monthEnd))
                ->count();

            $churnedSubscriptions[] = $subscriptions
                ->filter(fn ($subscription) => $subscription->ends_at !== null
                    && $subscription->ends_at->between($monthStart, $monthEnd))
                ->count();

            $activeSubscriptions = $subscriptions
                ->filter(fn ($subscription) => $this->isSubscriptionActiveDuring($subscription, $monthStart, $monthEnd));

            $activeUsers[] = $activeSubscriptions
                ->pluck('user_id')
                ->unique()
                ->count();

            $monthlyRevenueCents = $activeSubscriptions
                ->pluck('id')
                ->sum(fn ($subscriptionId) => $revenuePerSubscription->get($subscriptionId, 0));

            $revenue[] = round($monthlyRevenueCents / 100, 2);
        }

        return [
            'labels' => $labels,
            'new_subscriptions' => $newSubscriptions,
            'churned_subscriptions' => $churnedSubscriptions,
            'revenue' => $revenue,
            'active_users' => $activeUsers,
            'generated_at' => Carbon::now(),
        ];
    }

    /**
     * Build summary metrics based on the monthly dataset.
     *
     * @param  array<string, mixed>  $monthly
     * @return array<string, mixed>
     */
    private function buildSummary(array $monthly): array
    {
        $labels = $monthly['labels'] ?? [];
        $count = count($labels);
        $lastIndex = $count > 0 ? $count - 1 : null;

        $latestActiveUsers = $lastIndex !== null ? $monthly['active_users'][$lastIndex] : 0;
        $latestNew = $lastIndex !== null ? $monthly['new_subscriptions'][$lastIndex] : 0;
        $latestChurn = $lastIndex !== null ? $monthly['churned_subscriptions'][$lastIndex] : 0;
        $latestRevenue = $lastIndex !== null ? $monthly['revenue'][$lastIndex] : 0.0;

        $churnBase = max(1, $latestActiveUsers + $latestChurn);
        $churnRate = $latestChurn > 0 ? round(($latestChurn / $churnBase) * 100, 2) : 0.0;

        return [
            'active_users' => $latestActiveUsers,
            'new_subscriptions' => $latestNew,
            'churned_subscriptions' => $latestChurn,
            'monthly_recurring_revenue' => $latestRevenue,
            'total_revenue' => round(array_sum($monthly['revenue'] ?? []), 2),
            'churn_rate' => $churnRate,
            'generated_at' => $monthly['generated_at'] ?? Carbon::now(),
        ];
    }

    private function monthlyCacheKey(int $months): string
    {
        return "analytics.monthly.{$months}";
    }

    private function totalsCacheKey(int $months): string
    {
        return "analytics.totals.{$months}";
    }

    private function isActiveStatus(?string $status): bool
    {
        if ($status === null) {
            return false;
        }

        return in_array(strtolower($status), self::ACTIVE_STATUSES, true);
    }

    private function isSubscriptionActiveDuring(object $subscription, CarbonInterface $periodStart, CarbonInterface $periodEnd): bool
    {
        if (! $this->isActiveStatus($subscription->stripe_status)) {
            return false;
        }

        if ($subscription->created_at->gt($periodEnd)) {
            return false;
        }

        if ($subscription->ends_at !== null && $subscription->ends_at->lt($periodStart)) {
            return false;
        }

        return true;
    }
}
