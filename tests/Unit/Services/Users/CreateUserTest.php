<?php

namespace Tests\Unit\Services\Users;

use App\Mail\WelcomeUser;
use App\Models\User;
use App\Services\Users\CreateUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CreateUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_user_and_queues_a_welcome_mail(): void
    {
        Mail::fake();

        $service = app(CreateUser::class);

        $user = $service->handle([
            'name' => 'Test User',
            'email' => 'user@example.com',
            'password' => 'secret-password',
            'preferred_locale' => 'fr',
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertNotNull($user->getKey());
        $this->assertSame('user@example.com', $user->email);
        $this->assertSame('fr', $user->preferred_locale);
        $this->assertTrue(Hash::check('secret-password', $user->password));

        Mail::assertQueued(WelcomeUser::class, function (WelcomeUser $mail) use ($user) {
            return $mail->user->is($user)
                && $mail->queue === 'emails';
        });
    }
}
