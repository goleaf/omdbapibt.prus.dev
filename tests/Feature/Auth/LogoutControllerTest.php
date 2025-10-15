<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_is_logged_out_and_redirected_home(): void
    {
        $locale = config('app.fallback_locale');
        $user = User::factory()->create();

        $this->withSession([
            '_token' => 'initial-token',
            'custom' => 'value',
        ]);

        $this->actingAs($user);

        $response = $this->post(route('logout', ['locale' => $locale]));

        $response->assertRedirect(route('home', ['locale' => $locale]));
        $this->assertGuest();
        $this->assertFalse(session()->has('custom'));
        $this->assertNotSame('initial-token', session()->token());
    }

    public function test_guest_is_redirected_to_login_when_attempting_logout(): void
    {
        $locale = config('app.fallback_locale');

        $response = $this->post(route('logout', ['locale' => $locale]));

        $response->assertRedirect(route('login', ['locale' => $locale]));
    }
}
