<?php

namespace Tests\Feature\Livewire;

use App\Livewire\SubscriptionManage;
use App\Models\SubscriptionPayment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

class SubscriptionManageTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_history_renders_from_seeded_records(): void
    {
        config()->set('cashier.secret', null);

        $user = User::factory()->create(['stripe_id' => 'cus_test']);

        $subscription = $user->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_test_001',
            'stripe_status' => 'active',
            'stripe_price' => 'price_monthly',
            'quantity' => 1,
        ]);

        SubscriptionPayment::factory()->for($user)->for($subscription, 'subscription')->create([
            'invoice_id' => 'in_test_001',
            'invoice_number' => 'INV-LOCAL-001',
            'status' => 'paid',
            'amount' => 1499,
            'currency' => 'usd',
            'paid_at' => Carbon::now()->subDays(2),
        ]);

        SubscriptionPayment::factory()->for($user)->for($subscription, 'subscription')->create([
            'invoice_id' => 'in_test_002',
            'invoice_number' => 'INV-LOCAL-002',
            'status' => 'open',
            'amount' => 1499,
            'currency' => 'usd',
            'paid_at' => null,
        ]);

        $this->actingAs($user);

        Livewire::test(SubscriptionManage::class)
            ->assertSee('Payment history')
            ->assertSee('INV-LOCAL-001')
            ->assertSee('INV-LOCAL-002')
            ->assertSee('14.99 USD')
            ->assertSee('Open');
    }
}
