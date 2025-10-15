<?php

namespace Tests\Feature\Auth;

use App\Livewire\Auth\SignupForm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class SignupFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_validation_errors_are_displayed_for_missing_fields(): void
    {
        Livewire::test(SignupForm::class)
            ->call('register')
            ->assertHasErrors([
                'name' => 'required',
                'email' => 'required',
                'password' => 'required',
            ]);
    }

    public function test_email_must_be_valid(): void
    {
        Livewire::test(SignupForm::class)
            ->set('name', 'Jamie Invalid')
            ->set('email', 'not-an-email')
            ->set('password', 'Sup3rSecure!')
            ->call('register')
            ->assertHasErrors([
                'email' => 'email',
            ]);
    }

    public function test_password_must_meet_strength_requirements(): void
    {
        Livewire::test(SignupForm::class)
            ->set('name', 'Weak Password')
            ->set('email', 'weak@example.com')
            ->set('password', 'password')
            ->call('register')
            ->assertHasErrors(['password']);
    }

    public function test_successful_signup_creates_user_and_queues_welcome_mail(): void
    {
        app()->setLocale('en');

        Mail::fake();

        $email = 'jamie@example.com';

        Livewire::test(SignupForm::class)
            ->set('name', 'Jamie Example')
            ->set('email', $email)
            ->set('password', 'Sup3rSecure!')
            ->call('register')
            ->assertHasNoErrors()
            ->assertSessionHas('status')
            ->assertRedirect(route('dashboard', ['locale' => app()->getLocale()]));

        $user = User::where('email', $email)->first();

        $this->assertNotNull($user);
        $this->assertAuthenticatedAs($user);

        Mail::assertQueued('App\\Mail\\WelcomeMail', function (object $mail) use ($user): bool {
            return method_exists($mail, 'hasTo') && $mail->hasTo($user->email);
        });
    }

    public function test_authenticated_users_are_redirected_from_signup_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('signup', ['locale' => 'en']));

        $response->assertRedirect(route('dashboard', ['locale' => 'en']));
    }
}
