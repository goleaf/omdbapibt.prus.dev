<?php

namespace Tests\Feature\Auth;

use App\Livewire\Auth\LoginForm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use Tests\TestCase;

class LoginFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_login_with_valid_credentials(): void
    {
        app()->setLocale('en');

        $user = User::factory()->create([
            'email' => 'jane@example.com',
        ]);

        Livewire::test(LoginForm::class)
            ->set('email', $user->email)
            ->set('password', 'password')
            ->set('remember', true)
            ->call('login')
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard', ['locale' => app()->getLocale()]));

        $this->assertTrue(Auth::check());
        $this->assertSame($user->id, Auth::id());
    }

    public function test_validation_errors_are_displayed_for_missing_credentials(): void
    {
        Livewire::test(LoginForm::class)
            ->call('login')
            ->assertHasErrors([
                'email' => 'required',
                'password' => 'required',
            ]);
    }

    public function test_rate_limiting_is_applied_after_too_many_attempts(): void
    {
        app()->setLocale('en');

        $user = User::factory()->create([
            'email' => 'locked@example.com',
        ]);

        $ip = app('request')->ip();
        $throttleKey = strtolower($user->email).'|'.$ip;

        RateLimiter::clear($throttleKey);

        $component = Livewire::test(LoginForm::class);

        foreach (range(1, 5) as $_) {
            $component
                ->set('email', $user->email)
                ->set('password', 'invalid-password')
                ->call('login');
        }

        $remainingSeconds = RateLimiter::availableIn($throttleKey);

        $component
            ->set('email', $user->email)
            ->set('password', 'invalid-password')
            ->call('login')
            ->assertHasErrors([
                'email' => __('auth.throttle', [
                    'seconds' => $remainingSeconds,
                    'minutes' => (int) ceil($remainingSeconds / 60),
                ]),
            ]);

        $this->assertTrue(RateLimiter::tooManyAttempts($throttleKey, 5));
    }
}
