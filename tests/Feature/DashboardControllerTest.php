<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_view_receives_presented_data(): void
    {
        config([
            'services.stripe.trial_days' => 14,
            'services.stripe.prices.monthly' => 'price_test',
        ]);

        $user = User::factory()->create();

        $response = $this->withSession(['status' => 'Welcome back'])
            ->actingAs($user)
            ->get(route('dashboard', ['locale' => 'en']));

        $response->assertOk();
        $response->assertViewIs('dashboard');
        $response->assertViewHasAll([
            'statusMessage',
            'subscriptionState',
            'subscriptionStatus',
            'trialDays',
            'priceId',
            'trialEndsAtLabel',
            'graceEndsAtLabel',
            'nextInvoiceLabel',
        ]);

        $this->assertSame('Welcome back', $response->viewData('statusMessage'));
        $this->assertSame('none', $response->viewData('subscriptionState'));
        $this->assertSame('inactive', $response->viewData('subscriptionStatus'));
        $this->assertSame(14, $response->viewData('trialDays'));
        $this->assertSame('price_test', $response->viewData('priceId'));
        $this->assertSame('—', $response->viewData('trialEndsAtLabel'));
        $this->assertSame('—', $response->viewData('graceEndsAtLabel'));
        $this->assertSame('—', $response->viewData('nextInvoiceLabel'));
    }
}
