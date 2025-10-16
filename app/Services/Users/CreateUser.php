<?php

namespace App\Services\Users;

use App\Mail\WelcomeUser;
use App\Models\User;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Support\Facades\Mail;

class CreateUser
{
    public function __construct(private Hasher $hasher) {}

    /**
     * Persist a new user and queue a welcome email.
     *
     * @param  array{name:string,email:string,password:string,preferred_locale?:string}  $attributes
     */
    public function handle(array $attributes): User
    {
        $payload = $this->preparePayload($attributes);

        $user = User::create($payload);

        Mail::to($user)
            ->locale($user->preferred_locale ?? app()->getLocale())
            ->queue(new WelcomeUser($user));

        return $user;
    }

    /**
     * @param  array{name:string,email:string,password:string,preferred_locale?:string}  $attributes
     * @return array{name:string,email:string,password:string,preferred_locale:string}
     */
    protected function preparePayload(array $attributes): array
    {
        $preferredLocale = $attributes['preferred_locale'] ?? app()->getLocale();

        return [
            'name' => $attributes['name'],
            'email' => $attributes['email'],
            'password' => $this->hasher->make($attributes['password']),
            'preferred_locale' => $preferredLocale,
        ];
    }
}
