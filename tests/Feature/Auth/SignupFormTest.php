<?php

namespace Tests\Feature\Auth;

use App\Livewire\Auth\SignupForm;
use App\Mail\WelcomeUserMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class SignupFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_signup_requires_valid_input(): void
    {
        Mail::fake();

        Livewire::test(SignupForm::class)
            ->set('name', '')
            ->set('email', 'not-an-email')
            ->set('password', 'Password123!')
            ->set('password_confirmation', 'Mismatch123!')
            ->set('terms', false)
            ->call('submit')
            ->assertHasErrors([
                'name' => 'required',
                'email' => 'email',
                'password' => 'confirmed',
                'terms' => 'accepted',
            ]);

        Mail::assertNothingQueued();
    }

    public function test_user_can_register_successfully(): void
    {
        Mail::fake();

        $locale = config('translatable.fallback_locale', config('app.fallback_locale', 'en'));
        $password = 'Sup3rSecure!';

        Livewire::test(SignupForm::class)
            ->set('name', 'New Member')
            ->set('email', 'new@example.com')
            ->set('password', $password)
            ->set('password_confirmation', $password)
            ->set('terms', true)
            ->call('submit')
            ->assertRedirect(route('dashboard', ['locale' => $locale]));

        $this->assertAuthenticated();

        $user = User::where('email', 'new@example.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue(Hash::check($password, $user->password));
        $this->assertSame($locale, $user->preferred_locale);

        Mail::assertQueued(WelcomeUserMail::class, function (WelcomeUserMail $mail): bool {
            return $mail->hasTo('new@example.com');
        });
    }

    public function test_authenticated_user_is_redirected_from_signup(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $this->be($user);

        $locale = config('translatable.fallback_locale', config('app.fallback_locale', 'en'));

        Livewire::test(SignupForm::class)
            ->assertRedirect(route('dashboard', ['locale' => $locale]));

        Mail::assertNothingQueued();
    }
}
