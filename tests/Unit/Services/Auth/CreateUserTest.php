<?php

namespace Tests\Unit\Services\Auth;

use App\Mail\WelcomeUser;
use App\Models\User;
use App\Services\Auth\CreateUser;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CreateUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_user_and_queues_a_localized_welcome_mail(): void
    {
        Event::fake();
        Mail::fake();

        $service = app(CreateUser::class);

        $user = $service->create([
            'name' => 'Queue Tester',
            'email' => 'queue@example.com',
            'password' => 'Sup3rSecure!',
        ], 'fr');

        $this->assertInstanceOf(User::class, $user);

        $this->assertDatabaseHas('users', [
            'email' => 'queue@example.com',
            'preferred_locale' => 'fr',
        ]);

        Event::assertDispatched(Registered::class, function (Registered $event) use ($user): bool {
            return $event->user->is($user);
        });

        Mail::assertQueued(WelcomeUser::class, function (WelcomeUser $mail) use ($user): bool {
            return $mail->hasTo($user->email) && $mail->locale === 'fr';
        });
    }
}
