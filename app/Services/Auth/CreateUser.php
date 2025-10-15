<?php

namespace App\Services\Auth;

use App\Enums\UserRole;
use App\Mail\WelcomeUser;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;

class CreateUser
{
    /**
     * Persist a new user and queue a localized welcome email.
     */
    public function create(array $attributes, ?string $locale = null): User
    {
        $locale ??= app()->getLocale();

        $user = DB::transaction(function () use ($attributes, $locale): User {
            $user = User::create([
                'name' => $attributes['name'],
                'email' => $attributes['email'],
                'password' => $attributes['password'],
                'role' => UserRole::User,
                'preferred_locale' => $locale,
            ]);

            Event::dispatch(new Registered($user));

            DB::afterCommit(function () use ($user, $locale): void {
                Mail::to($user)
                    ->locale($locale)
                    ->queue(new WelcomeUser($user));
            });

            return $user;
        });

        return $user;
    }
}
