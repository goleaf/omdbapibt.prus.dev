<?php

namespace Database\Seeders;

use App\Models\SubscriptionPayment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Laravel\Cashier\Subscription as CashierSubscription;

class SubscriptionPaymentSeeder extends Seeder
{
    /**
     * Seed historical subscription payments to support offline billing views.
     */
    public function run(): void
    {
        if (! Schema::hasTable('subscription_payments')
            || ! Schema::hasTable('subscriptions')
            || ! Schema::hasTable('users')) {
            return;
        }

        if (SubscriptionPayment::query()->exists()) {
            return;
        }

        $subscriptions = CashierSubscription::query()->with('user')->get();

        if ($subscriptions->isEmpty()) {
            $subscriptions = $this->seedExampleSubscriptions();
        }

        if ($subscriptions->isEmpty()) {
            return;
        }

        $faker = fake();

        foreach ($subscriptions as $subscription) {
            if (! $subscription->user instanceof User) {
                continue;
            }

            $historyLength = $faker->numberBetween(3, 6);
            $baseAmount = $faker->numberBetween(999, 2499);
            $currency = $faker->randomElement(['usd', 'eur', 'gbp']);

            for ($index = 0; $index < $historyLength; $index++) {
                $isLatest = $index === $historyLength - 1;
                $status = $isLatest
                    ? $faker->randomElement(['paid', 'paid', 'paid', 'open', 'past_due'])
                    : ($faker->boolean(20) ? 'refunded' : 'paid');

                $periodDate = Carbon::now()
                    ->subMonths($historyLength - $index)
                    ->startOfMonth()
                    ->addDays($faker->numberBetween(0, 6));

                $paidAt = in_array($status, ['paid', 'refunded'], true)
                    ? $periodDate->copy()->addDays($faker->numberBetween(0, 3))
                    : null;

                SubscriptionPayment::query()->create([
                    'user_id' => $subscription->user_id,
                    'subscription_id' => $subscription->getKey(),
                    'amount' => $baseAmount,
                    'currency' => $currency,
                    'status' => $status,
                    'invoice_id' => sprintf('in_%s%s', Str::lower(Str::random(10)), $subscription->getKey()),
                    'invoice_number' => sprintf('INV-%s-%05d', $periodDate->format('Y'), ($subscription->getKey() * 10) + $index + 1),
                    'paid_at' => $paidAt,
                    'created_at' => $periodDate,
                    'updated_at' => $paidAt ?? $periodDate,
                ]);
            }
        }
    }

    /**
     * Seed example subscriptions when none exist so payment history has context.
     *
     * @return Collection<int, CashierSubscription>
     */
    protected function seedExampleSubscriptions(): Collection
    {
        $users = User::query()->limit(50)->get();

        if ($users->isEmpty()) {
            return collect();
        }

        $statuses = ['active', 'trialing', 'past_due', 'canceled'];
        $faker = fake();

        return $users->map(function (User $user, int $index) use ($statuses, $faker): CashierSubscription {
            $status = $statuses[$index % count($statuses)];
            $trialEndsAt = $status === 'trialing'
                ? Carbon::now()->addDays($faker->numberBetween(3, 14))
                : null;
            $endsAt = in_array($status, ['past_due', 'canceled'], true)
                ? ($status === 'past_due'
                    ? Carbon::now()->addDays($faker->numberBetween(3, 10))
                    : Carbon::now()->subDays($faker->numberBetween(5, 30)))
                : null;

            return $user->subscriptions()->create([
                'type' => 'default',
                'stripe_id' => sprintf('sub_%s', Str::lower(Str::random(26))),
                'stripe_status' => $status,
                'stripe_price' => 'price_standard_monthly',
                'quantity' => 1,
                'trial_ends_at' => $trialEndsAt,
                'ends_at' => $endsAt,
            ]);
        });
    }
}
