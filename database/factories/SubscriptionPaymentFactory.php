<?php

namespace Database\Factories;

use App\Models\SubscriptionPayment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @extends Factory<SubscriptionPayment>
 */
class SubscriptionPaymentFactory extends Factory
{
    protected $model = SubscriptionPayment::class;

    public function definition(): array
    {
        $status = fake()->randomElement(['paid', 'paid', 'paid', 'refunded', 'past_due', 'open']);
        $paidAt = in_array($status, ['paid', 'refunded'], true)
            ? Carbon::instance(fake()->dateTimeBetween('-11 months', 'now'))
            : null;

        return [
            'user_id' => User::factory(),
            'subscription_id' => null,
            'amount' => fake()->numberBetween(999, 2499),
            'currency' => fake()->randomElement(['usd', 'eur', 'gbp']),
            'status' => $status,
            'invoice_id' => 'in_'.Str::lower(Str::random(24)),
            'invoice_number' => 'INV-'.fake()->numerify('2025-#####'),
            'paid_at' => $paidAt,
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (SubscriptionPayment $payment): void {
            if ($payment->subscription_id || $payment->subscription) {
                return;
            }

            $user = $payment->user;

            if (! $user instanceof User) {
                $user = User::query()->find($payment->user_id);
            }

            if (! $user instanceof User) {
                $user = User::factory()->create();
                $payment->user()->associate($user);
                $payment->user_id = $user->getKey();
            }

            $subscription = $user->subscriptions()->create([
                'type' => 'default',
                'stripe_id' => 'sub_'.Str::lower(Str::random(24)),
                'stripe_status' => 'active',
                'stripe_price' => 'price_'.Str::lower(Str::random(10)),
                'quantity' => 1,
            ]);

            $payment->subscription_id = $subscription->getKey();
        });
    }
}
