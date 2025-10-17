<?php

namespace Tests\Feature\Auth;

use App\Livewire\Auth\ForgotPasswordForm;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class ForgotPasswordFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_link_can_be_requested(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'resetme@example.com',
        ]);

        Livewire::test(ForgotPasswordForm::class)
            ->set('email', 'resetme@example.com')
            ->call('sendResetLink')
            ->assertHasNoErrors()
            ->assertSet('statusMessage', trans('passwords.sent'));

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_validation_errors_are_reported(): void
    {
        Livewire::test(ForgotPasswordForm::class)
            ->set('email', 'not-an-email')
            ->call('sendResetLink')
            ->assertHasErrors(['email' => 'email']);
    }
}
