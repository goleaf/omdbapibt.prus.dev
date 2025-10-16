<?php

namespace Tests\Feature\Database;

use App\Enums\BillingInterval;
use App\Enums\SubscriptionStatus;
use App\Enums\UserRole;
use App\Livewire\Admin\AnalyticsDashboard;
use App\Models\User;
use App\Services\Subscriptions\SubscriptionAnalyticsService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use Tests\TestCase;

class DatabaseSeederSubscriptionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

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

        config()->set('subscriptions.active_statuses', SubscriptionStatus::activeValues());
        config()->set('subscriptions.cache.metrics_ttl', 60);
    }

    public function test_seeded_users_receive_premium_access(): void
    {
        $this->seed(DatabaseSeeder::class);

        $seededUsers = User::query()->orderBy('id')->limit(4)->get();

        $this->assertCount(4, $seededUsers);
        $seededUsers->each(function (User $user): void {
            $this->assertNotNull($user->stripe_id);
            $this->assertTrue($user->hasPremiumAccess());
        });

        $monthlyPriceId = config('subscriptions.plans.premium_monthly.price_id');
        $yearlyPriceId = config('subscriptions.plans.premium_yearly.price_id');

        $this->assertDatabaseHas('subscriptions', [
            'stripe_price' => $monthlyPriceId,
            'stripe_status' => SubscriptionStatus::Active->value,
        ]);

        $this->assertDatabaseHas('subscription_items', [
            'stripe_price' => $monthlyPriceId,
            'meter_id' => 'meter_premium_monthly',
            'meter_event_name' => 'stream.premium_monthly',
        ]);

        $this->assertDatabaseHas('subscription_items', [
            'stripe_price' => $yearlyPriceId,
            'meter_id' => 'meter_premium_yearly',
            'meter_event_name' => 'stream.premium_yearly',
        ]);
    }

    public function test_seeded_environment_renders_subscription_analytics(): void
    {
        $this->seed(DatabaseSeeder::class);

        Cache::forget('analytics:subscriptions:metrics');

        /** @var SubscriptionAnalyticsService $service */
        $service = app(SubscriptionAnalyticsService::class);
        $metrics = $service->getMetrics(true);

        $this->assertGreaterThan(0, $metrics['totals']['active_subscriptions']);
        $this->assertNotEmpty($metrics['plans']);
        $this->assertNotEmpty($metrics['trend']);

        $admin = User::query()->where('role', UserRole::Admin->value)->firstOrFail();

        Livewire::actingAs($admin)
            ->test(AnalyticsDashboard::class)
            ->assertSet('plans', function (array $plans): bool {
                $this->assertNotEmpty($plans);

                return true;
            })
            ->assertSet('charts', function (array $charts): bool {
                $this->assertNotEmpty($charts['trend'] ?? []);

                return true;
            })
            ->assertSet('currency', 'USD');
    }
}
