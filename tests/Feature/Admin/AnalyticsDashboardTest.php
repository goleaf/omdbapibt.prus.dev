<?php

namespace Tests\Feature\Admin;

use App\Enums\BillingInterval;
use App\Enums\SubscriptionStatus;
use App\Livewire\Admin\AnalyticsDashboard;
use App\Models\User;
use App\Services\Subscriptions\SubscriptionAnalyticsService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class AnalyticsDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_aggregates_subscription_metrics(): void
    {
        $this->travelTo(Carbon::parse('2025-06-15 12:00:00'));

        config()->set('subscriptions.plans', [
            'premium_monthly' => [
                'name' => 'Premium Monthly',
                'price_id' => 'price_monthly',
                'amount' => 1299,
                'currency' => 'usd',
                'interval' => BillingInterval::Month,
                'interval_count' => 1,
            ],
            'premium_yearly' => [
                'name' => 'Premium Yearly',
                'price_id' => 'price_yearly',
                'amount' => 12999,
                'currency' => 'usd',
                'interval' => BillingInterval::Year,
                'interval_count' => 1,
            ],
        ]);

        config()->set(
            'subscriptions.active_statuses',
            SubscriptionStatus::values([SubscriptionStatus::Active, SubscriptionStatus::Trialing])
        );
        config()->set('subscriptions.cache.metrics_ttl', 60);

        $monthlyUser = User::factory()->create();
        $yearlyUser = User::factory()->create();
        $trialUser = User::factory()->create();
        $churnedUser = User::factory()->create();

        DB::table('subscriptions')->insert([
            [
                'id' => 1,
                'user_id' => $monthlyUser->id,
                'type' => 'default',
                'stripe_id' => 'sub_monthly',
                'stripe_status' => SubscriptionStatus::Active->value,
                'stripe_price' => 'price_monthly',
                'quantity' => 1,
                'trial_ends_at' => null,
                'ends_at' => null,
                'created_at' => now()->subMonths(1),
                'updated_at' => now()->subMonths(1),
            ],
            [
                'id' => 2,
                'user_id' => $yearlyUser->id,
                'type' => 'default',
                'stripe_id' => 'sub_yearly',
                'stripe_status' => SubscriptionStatus::Active->value,
                'stripe_price' => 'price_yearly',
                'quantity' => 1,
                'trial_ends_at' => null,
                'ends_at' => null,
                'created_at' => now()->subMonths(2),
                'updated_at' => now()->subMonths(2),
            ],
            [
                'id' => 3,
                'user_id' => $trialUser->id,
                'type' => 'default',
                'stripe_id' => 'sub_trial',
                'stripe_status' => SubscriptionStatus::Trialing->value,
                'stripe_price' => 'price_monthly',
                'quantity' => 1,
                'trial_ends_at' => now()->addDays(7),
                'ends_at' => null,
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
            [
                'id' => 4,
                'user_id' => $churnedUser->id,
                'type' => 'default',
                'stripe_id' => 'sub_churned',
                'stripe_status' => SubscriptionStatus::Canceled->value,
                'stripe_price' => 'price_monthly',
                'quantity' => 1,
                'trial_ends_at' => null,
                'ends_at' => now()->subDays(10),
                'created_at' => now()->subMonths(4),
                'updated_at' => now()->subDays(10),
            ],
        ]);

        DB::table('subscription_items')->insert([
            [
                'id' => 1,
                'subscription_id' => 1,
                'stripe_id' => 'si_monthly',
                'stripe_product' => 'prod_monthly',
                'stripe_price' => 'price_monthly',
                'quantity' => 1,
                'created_at' => now()->subMonths(1),
                'updated_at' => now()->subMonths(1),
            ],
            [
                'id' => 2,
                'subscription_id' => 2,
                'stripe_id' => 'si_yearly',
                'stripe_product' => 'prod_yearly',
                'stripe_price' => 'price_yearly',
                'quantity' => 1,
                'created_at' => now()->subMonths(2),
                'updated_at' => now()->subMonths(2),
            ],
            [
                'id' => 3,
                'subscription_id' => 3,
                'stripe_id' => 'si_trial',
                'stripe_product' => 'prod_monthly',
                'stripe_price' => 'price_monthly',
                'quantity' => 1,
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
        ]);

        /** @var SubscriptionAnalyticsService $service */
        $service = app(SubscriptionAnalyticsService::class);

        Cache::forget('analytics:subscriptions:metrics');

        $metrics = $service->getMetrics(true);

        $this->assertSame(3, $metrics['totals']['active_subscriptions']);
        $this->assertSame(3, $metrics['totals']['active_customers']);
        $this->assertSame(1, $metrics['totals']['trialing_subscriptions']);
        $this->assertSame(1, $metrics['totals']['churned_last_30_days']);
        $this->assertSame('USD', $metrics['currency']);
        $this->assertEqualsWithDelta(36.81, $metrics['totals']['mrr'], 0.01);
        $this->assertEqualsWithDelta(441.72, $metrics['totals']['arr'], 0.01);
        $this->assertCount(2, $metrics['plans']);
        $this->assertNotEmpty($metrics['trend']);

        $cached = $service->getMetrics();
        $this->assertSame($metrics, $cached);

        DB::table('subscriptions')->insert([
            'id' => 5,
            'user_id' => User::factory()->create()->id,
            'type' => 'default',
            'stripe_id' => 'sub_new',
            'stripe_status' => SubscriptionStatus::Active->value,
            'stripe_price' => 'price_monthly',
            'quantity' => 1,
            'trial_ends_at' => null,
            'ends_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('subscription_items')->insert([
            'id' => 4,
            'subscription_id' => 5,
            'stripe_id' => 'si_new',
            'stripe_product' => 'prod_monthly',
            'stripe_price' => 'price_monthly',
            'quantity' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $staleMetrics = $service->getMetrics();
        $this->assertSame($metrics, $staleMetrics, 'Cached metrics should be returned without forcing refresh.');

        $freshMetrics = $service->getMetrics(true);
        $this->assertSame(4, $freshMetrics['totals']['active_subscriptions']);
    }

    public function test_admin_dashboard_component_renders_for_admins(): void
    {
        $this->travelTo(Carbon::parse('2025-06-15 12:00:00'));

        config()->set('subscriptions.plans', []);

        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(AnalyticsDashboard::class)
            ->assertSet('currency', 'USD')
            ->assertSee('Subscription Analytics');
    }

    public function test_non_admin_cannot_access_dashboard_route(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.analytics'))
            ->assertForbidden();
    }
}
