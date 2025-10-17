<?php

namespace Tests\Feature\Auth;

use App\Livewire\Auth\SignupForm;
use App\Mail\WelcomeUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class SignupFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_register_via_livewire(): void
    {
        Mail::fake();

        Livewire::test(SignupForm::class)
            ->set('name', 'New Member')
            ->set('email', 'new-member@example.com')
            ->set('password', 'secret-pass')
            ->set('agreementsAccepted', true)
            ->call('register')
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard', ['locale' => app()->getLocale()]));

        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'email' => 'new-member@example.com',
        ]);

        Mail::assertQueued(WelcomeUser::class);
    }

    public function test_agreements_are_required_to_register(): void
    {
        Mail::fake();

        Livewire::test(SignupForm::class)
            ->set('name', 'New Member')
            ->set('email', 'new-member@example.com')
            ->set('password', 'secret-pass')
            ->call('register')
            ->assertHasErrors(['agreementsAccepted' => 'accepted']);
    }
}
