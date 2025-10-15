<?php

namespace App\Support\Auth;

use App\Mail\WelcomeUserMail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class CreateUser
{
    /**
     * @param  array{name:string,email:string,password:string,preferred_locale?:string}  $input
     */
    public function handle(array $input): User
    {
        $user = new User;

        $user->name = $input['name'];
        $user->email = strtolower($input['email']);
        $user->password = Hash::make($input['password']);
        $user->preferred_locale = $input['preferred_locale']
            ?? config('translatable.fallback_locale', config('app.fallback_locale', 'en'));

        $user->save();

        Mail::to($user)->queue(new WelcomeUserMail($user));

        return $user;
    }
}
