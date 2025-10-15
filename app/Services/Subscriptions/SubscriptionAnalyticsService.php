<?php

namespace App\Services\Subscriptions;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SubscriptionAnalyticsService
{
    protected const CACHE_KEY = 'analytics:subscriptions:metrics';

    public function getMetrics(bool $forceRefresh = false): array
    {
        $ttl = (int) (Config::get('subscriptions.cache.metrics_ttl')
            ?: Config::get('cache_ttls.analytics.subscription_metrics', 300));

        if ($forceRefresh) {
            Cache::forget(self::CACHE_KEY);
        }

        return Cache::remember(self::CACHE_KEY, $ttl, function (): array {
            return $this->calculateMetrics();
        });
    }

    public function planCatalog(): array
    {
        $plans = Config::get('subscriptions.plans', []);
        $currency = strtoupper(Config::get('cashier.currency', 'usd'));

        $indexed = [];

        foreach ($plans as $slug => $plan) {
            $plan = array_merge([
                'slug' => $slug,
                'name' => Str::headline(str_replace('_', ' ', (string) $slug)),
                'amount' => 0,
                'currency' => $currency,
                'interval' => 'month',
                'interval_count' => 1,
                'price_id' => null,
            ], $plan);

            $priceId = $plan['price_id'] ?: $slug;
            $plan['price_id'] = $priceId;
            $plan['currency'] = strtoupper((string) $plan['currency']);

            $indexed[$priceId] = $plan;
        }

        return $indexed;
    }

    protected function calculateMetrics(): array
    {
        $activeStatuses = Config::get('subscriptions.active_statuses', ['active', 'trialing']);
        $currency = strtoupper(Config::get('cashier.currency', 'usd'));
        $planCatalog = $this->planCatalog();

        $activeSubscriptions = DB::table('subscriptions')
            ->whereNull('ends_at')
            ->whereIn('stripe_status', $activeStatuses)
            ->count();

        $activeCustomers = DB::table('subscriptions')
            ->whereNull('ends_at')
            ->whereIn('stripe_status', $activeStatuses)
            ->distinct()
            ->count('user_id');

        $trialingSubscriptions = DB::table('subscriptions')
            ->whereNull('ends_at')
            ->where('stripe_status', 'trialing')
            ->count();

        $churnedLast30Days = DB::table('subscriptions')
            ->whereNotNull('ends_at')
            ->where('ends_at', '>=', Carbon::now()->subDays(30))
            ->count();

        $activeItems = DB::table('subscription_items')
            ->join('subscriptions', 'subscriptions.id', '=', 'subscription_items.subscription_id')
            ->whereNull('subscriptions.ends_at')
            ->whereIn('subscriptions.stripe_status', $activeStatuses)
            ->get([
                'subscription_items.subscription_id',
                'subscription_items.stripe_price',
                'subscription_items.quantity',
            ]);

        $planBreakdown = [];
        $totalMrr = 0.0;

        foreach ($activeItems->groupBy('stripe_price') as $priceId => $items) {
            $plan = $planCatalog[$priceId] ?? $this->fallbackPlan((string) $priceId, $currency);
            $subscriptionsCount = $items->pluck('subscription_id')->unique()->count();
            $quantitySum = $items->sum(function ($item): int {
                return (int) ($item->quantity ?? 1);
            });

            $monthlyPrice = $this->monthlyAmount($plan);
            $planMrr = round($monthlyPrice * $quantitySum, 2);

            $planBreakdown[] = [
                'price_id' => (string) $priceId,
                'name' => $plan['name'],
                'slug' => $plan['slug'],
                'subscribers' => $subscriptionsCount,
                'quantity' => $quantitySum,
                'monthly_price' => $monthlyPrice,
                'mrr' => $planMrr,
                'currency' => $plan['currency'],
                'interval' => $plan['interval'],
                'interval_count' => (int) $plan['interval_count'],
            ];

            $totalMrr += $planMrr;
        }

        usort($planBreakdown, function (array $a, array $b): int {
            return $b['mrr'] <=> $a['mrr'];
        });

        $monthlyTrend = $this->monthlyTrend($planCatalog, $currency);

        return [
            'generated_at' => Carbon::now()->toDateTimeString(),
            'currency' => $currency,
            'totals' => [
                'active_subscriptions' => $activeSubscriptions,
                'active_customers' => $activeCustomers,
                'trialing_subscriptions' => $trialingSubscriptions,
                'churned_last_30_days' => $churnedLast30Days,
                'mrr' => round($totalMrr, 2),
                'arr' => round($totalMrr * 12, 2),
            ],
            'plans' => $planBreakdown,
            'trend' => $monthlyTrend,
            'catalog' => array_values($planCatalog),
        ];
    }

    protected function monthlyTrend(array $planCatalog, string $currency): array
    {
        $startMonth = Carbon::now()->startOfMonth()->subMonths(5);
        $months = collect(range(0, 5))->map(function (int $offset) use ($startMonth): Carbon {
            return $startMonth->copy()->addMonths($offset);
        });

        $subscriptions = DB::table('subscriptions')
            ->where('created_at', '>=', $months->first()->copy()->startOfMonth())
            ->get([
                'id',
                'created_at',
            ]);

        $itemsBySubscription = DB::table('subscription_items')
            ->whereIn('subscription_id', $subscriptions->pluck('id'))
            ->get([
                'subscription_id',
                'stripe_price',
                'quantity',
            ])
            ->groupBy('subscription_id');

        return $months->map(function (Carbon $month) use ($subscriptions, $itemsBySubscription, $planCatalog, $currency): array {
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();

            $monthlySubscriptions = $subscriptions->filter(function ($subscription) use ($start, $end): bool {
                $createdAt = Carbon::parse($subscription->created_at);

                return $createdAt->betweenIncluded($start, $end);
            });

            $count = $monthlySubscriptions->count();
            $revenue = $monthlySubscriptions->sum(function ($subscription) use ($itemsBySubscription, $planCatalog, $currency): float {
                $items = $itemsBySubscription->get($subscription->id, collect());

                return $this->calculateSubscriptionMonthlyRevenue($items, $planCatalog, $currency);
            });

            return [
                'month' => $month->format('Y-m'),
                'label' => $month->format('M Y'),
                'signups' => $count,
                'revenue' => round($revenue, 2),
            ];
        })->values()->all();
    }

    protected function calculateSubscriptionMonthlyRevenue(Collection $items, array $planCatalog, string $currency): float
    {
        return $items->sum(function ($item) use ($planCatalog, $currency): float {
            $plan = $planCatalog[$item->stripe_price] ?? $this->fallbackPlan((string) $item->stripe_price, $currency);
            $quantity = (int) ($item->quantity ?? 1);

            return $this->monthlyAmount($plan) * $quantity;
        });
    }

    protected function monthlyAmount(array $plan): float
    {
        $amount = (int) ($plan['amount'] ?? 0);
        $interval = strtolower((string) ($plan['interval'] ?? 'month'));
        $intervalCount = max(1, (int) ($plan['interval_count'] ?? 1));
        $totalMonths = $this->monthsForInterval($interval) * $intervalCount;

        if ($totalMonths <= 0) {
            return 0.0;
        }

        return round(($amount / 100) / $totalMonths, 2);
    }

    protected function monthsForInterval(string $interval): float
    {
        return match ($interval) {
            'year' => 12.0,
            'quarter' => 3.0,
            'week' => 0.25,
            'day' => 1 / 30,
            default => 1.0,
        };
    }

    protected function fallbackPlan(string $priceId, string $currency): array
    {
        return [
            'slug' => $priceId,
            'name' => $priceId,
            'price_id' => $priceId,
            'amount' => 0,
            'currency' => $currency,
            'interval' => 'month',
            'interval_count' => 1,
        ];
    }
}
