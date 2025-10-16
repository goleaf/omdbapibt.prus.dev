<?php

namespace Database\Seeders;

use App\Enums\SubscriptionStatus;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SubscriptionSeeder extends Seeder
{
    /**
     * Seed representative subscriptions and items for deterministic users.
     */
    public function run(): void
    {
        if (! Schema::hasTable('users')
            || ! Schema::hasTable('subscriptions')
            || ! Schema::hasTable('subscription_items')) {
            return;
        }

        $users = User::query()
            ->orderBy('id')
            ->limit(5)
            ->get();

        if ($users->isEmpty()) {
            return;
        }

        $planCatalog = $this->planCatalog();

        if ($planCatalog->isEmpty()) {
            return;
        }

        $now = Carbon::now();

        $scenarios = collect([
            [
                'plan' => 'premium_monthly',
                'status' => SubscriptionStatus::Active,
                'quantity' => 1,
                'created_at' => $now->copy()->subMonths(3),
                'trial_ends_at' => null,
                'ends_at' => null,
            ],
            [
                'plan' => 'premium_yearly',
                'status' => SubscriptionStatus::Active,
                'quantity' => 2,
                'created_at' => $now->copy()->subMonths(7),
                'trial_ends_at' => null,
                'ends_at' => null,
            ],
            [
                'plan' => 'premium_monthly',
                'status' => SubscriptionStatus::Trialing,
                'quantity' => 1,
                'created_at' => $now->copy()->subDays(10),
                'trial_ends_at' => $now->copy()->addDays(5),
                'ends_at' => null,
            ],
            [
                'plan' => 'premium_monthly',
                'status' => SubscriptionStatus::Active,
                'quantity' => 1,
                'created_at' => $now->copy()->subMonth(),
                'trial_ends_at' => null,
                'ends_at' => $now->copy()->addDays(7),
            ],
            [
                'plan' => 'premium_monthly',
                'status' => SubscriptionStatus::Canceled,
                'quantity' => 1,
                'created_at' => $now->copy()->subMonths(4),
                'trial_ends_at' => null,
                'ends_at' => $now->copy()->subDays(6),
            ],
        ]);

        $hasMeterId = Schema::hasColumn('subscription_items', 'meter_id');
        $hasMeterEvent = Schema::hasColumn('subscription_items', 'meter_event_name');

        $scenarios->each(function (array $scenario, int $index) use ($users, $planCatalog, $hasMeterEvent, $hasMeterId): void {
            $user = $users->get($index);

            if (! $user) {
                return;
            }

            $plan = $planCatalog->get($scenario['plan']);

            if (! $plan) {
                return;
            }

            if (! $user->stripe_id) {
                $user->forceFill([
                    'stripe_id' => sprintf('cus_%04d', $user->getKey()),
                    'pm_type' => 'card',
                    'pm_last_four' => '4242',
                ])->save();
            }

            $priceId = $plan['price_id'];
            $subscriptionStripeId = sprintf('sub_%s_%04d', Str::slug($plan['slug'], '_'), $user->getKey());
            $itemStripeId = sprintf('si_%s_%04d', Str::slug($plan['slug'], '_'), $user->getKey());
            $timestamps = [
                'created_at' => $scenario['created_at'],
                'updated_at' => $scenario['created_at'],
            ];

            DB::table('subscriptions')->updateOrInsert(
                ['stripe_id' => $subscriptionStripeId],
                [
                    'user_id' => $user->getKey(),
                    'type' => 'default',
                    'stripe_status' => $scenario['status']->value,
                    'stripe_price' => $priceId,
                    'quantity' => $scenario['quantity'],
                    'trial_ends_at' => $scenario['trial_ends_at'],
                    'ends_at' => $scenario['ends_at'],
                ] + $timestamps
            );

            $subscriptionId = DB::table('subscriptions')
                ->where('stripe_id', $subscriptionStripeId)
                ->value('id');

            if (! $subscriptionId) {
                return;
            }

            $itemAttributes = [
                'subscription_id' => $subscriptionId,
                'stripe_product' => sprintf('prod_%s', Str::slug($plan['slug'], '_')),
                'stripe_price' => $priceId,
                'quantity' => $scenario['quantity'],
            ] + $timestamps;

            if ($hasMeterId) {
                $itemAttributes['meter_id'] = sprintf('meter_%s', Str::slug($plan['slug'], '_'));
            }

            if ($hasMeterEvent) {
                $itemAttributes['meter_event_name'] = sprintf('stream.%s', Str::slug($plan['slug'], '_'));
            }

            DB::table('subscription_items')->updateOrInsert(
                ['stripe_id' => $itemStripeId],
                $itemAttributes
            );
        });
    }

    protected function planCatalog(): Collection
    {
        $plans = Config::get('subscriptions.plans', []);

        return collect($plans)->mapWithKeys(function (array $plan, string $slug): array {
            $slug = (string) $slug;
            $priceId = $plan['price_id'] ?? $slug;

            if (! is_string($priceId) || $priceId === '') {
                $priceId = $slug;
            }

            return [$slug => [
                'slug' => $slug,
                'name' => $plan['name'] ?? Str::headline(str_replace('_', ' ', $slug)),
                'price_id' => $priceId,
            ]];
        });
    }
}
