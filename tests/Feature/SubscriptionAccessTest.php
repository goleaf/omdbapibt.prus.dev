<?php

namespace Tests\Feature;

use App\Enums\SubscriptionStatus;
use App\Livewire\Browse\BrowsePage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SubscriptionAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_sees_locked_browse_page(): void
    {
        $this->get(route('browse', ['locale' => 'en']))
            ->assertOk()
            ->assertSee('Premium membership required')
            ->assertSeeLivewire(BrowsePage::class);
    }

    public function test_subscriber_sees_unlocked_browse_page(): void
    {
        $user = User::factory()->create(['stripe_id' => 'cus_browse']);
        $user->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_browse',
            'stripe_status' => SubscriptionStatus::Active->value,
            'stripe_price' => 'price_monthly',
            'quantity' => 1,
        ]);

        $this->actingAs($user)
            ->get(route('browse', ['locale' => 'en']))
            ->assertOk()
            ->assertSee('Trending right now')
            ->assertDontSee('Premium membership required');
    }

    public function test_livewire_component_marks_guest_as_locked(): void
    {
        Livewire::test(BrowsePage::class)
            ->assertSet('locked', true)
            ->assertSee('Premium membership required');
    }

    public function test_livewire_component_marks_subscriber_as_unlocked(): void
    {
        $user = User::factory()->create(['stripe_id' => 'cus_livewire']);
        $user->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_livewire',
            'stripe_status' => SubscriptionStatus::Active->value,
            'stripe_price' => 'price_monthly',
            'quantity' => 1,
        ]);

        Livewire::actingAs($user)
            ->test(BrowsePage::class)
            ->assertSet('locked', false)
            ->assertSee('Trending right now')
            ->assertDontSee('Premium membership required');
    }

    public function test_non_subscriber_is_redirected_to_checkout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('account.watch-history', ['locale' => 'en']))
            ->assertRedirect(route('checkout', ['locale' => 'en']))
            ->assertSessionHas('error');
    }

    public function test_checkout_redirects_subscribers_back_to_browse(): void
    {
        $user = User::factory()->create(['stripe_id' => 'cus_test']);
        $user->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_test',
            'stripe_status' => SubscriptionStatus::Active->value,
            'stripe_price' => 'price_monthly',
            'quantity' => 1,
        ]);

        $this->actingAs($user)
            ->get(route('checkout', ['locale' => 'en']))
            ->assertRedirect(route('browse', ['locale' => 'en']))
            ->assertSessionHas('status');
    }

    public function test_subscribers_can_access_watch_history(): void
    {
        $user = User::factory()->create(['stripe_id' => 'cus_live']);
        $user->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_live',
            'stripe_status' => SubscriptionStatus::Active->value,
            'stripe_price' => 'price_monthly',
            'quantity' => 1,
        ]);

        $this->actingAs($user)
            ->get(route('account.watch-history', ['locale' => 'en']))
            ->assertOk();
    }
}
