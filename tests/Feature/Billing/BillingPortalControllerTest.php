<?php

namespace Tests\Feature\Billing;

use App\Models\User;
use Illuminate\Support\Facades\URL;
use Mockery;
use Tests\TestCase;

class BillingPortalControllerTest extends TestCase
{
    public function test_guest_is_redirected_to_login(): void
    {
        $locale = config('app.fallback_locale');

        $response = $this->get(route('billing.portal', ['locale' => $locale]));

        $response->assertRedirect(route('login', ['locale' => $locale]));
    }

    public function test_authenticated_user_is_redirected_to_stripe_billing_portal(): void
    {
        $locale = config('app.fallback_locale');
        URL::defaults(['locale' => $locale]);

        $user = Mockery::mock(User::class)->makePartial();
        $user->forceFill([
            'id' => 1,
            'name' => 'Subscriber One',
            'email' => 'subscriber@example.com',
            'stripe_id' => 'cus_existing',
        ]);
        $user->exists = true;

        $user->shouldReceive('getAuthIdentifier')->andReturn(1);
        $user->shouldReceive('getAuthIdentifierName')->andReturn('id');
        $user->shouldReceive('getKey')->andReturn(1);
        $user->shouldReceive('save')->andReturnTrue();

        $user->shouldReceive('createOrGetStripeCustomer')
            ->once()
            ->andReturnSelf();

        $dashboardUrl = route('dashboard', ['locale' => $locale]);
        $portalUrl = 'https://billing.stripe.com/session/test_portal';

        $user->shouldReceive('billingPortalUrl')
            ->once()
            ->with($dashboardUrl)
            ->andReturn($portalUrl);

        $this->actingAs($user);

        $response = $this->get(route('billing.portal', ['locale' => $locale]));

        $response->assertRedirect($portalUrl);
    }
}
