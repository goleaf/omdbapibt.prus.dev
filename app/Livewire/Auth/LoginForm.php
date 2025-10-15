<?php

namespace App\Livewire\Auth;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Component;

class LoginForm extends Component
{
    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    public function login(): void
    {
        if ($this->isRateLimited()) {
            return;
        }

        $validated = $this->validate($this->rules());

        if (! Auth::attempt([
            'email' => $validated['email'],
            'password' => $validated['password'],
        ], (bool) ($validated['remember'] ?? false))) {
            RateLimiter::hit($this->throttleKey(), 60);

            $this->addError('email', __('auth.failed'));

            return;
        }

        RateLimiter::clear($this->throttleKey());

        session()->regenerate();

        $this->reset('password');

        $this->redirectIntended(
            route('dashboard', ['locale' => app()->getLocale()]),
            navigate: true
        );
    }

    public function render(): View
    {
        return view('livewire.auth.login-form')
            ->layout('layouts.app', [
                'title' => __('Sign in'),
                'header' => __('Welcome back'),
                'subheader' => __('Log in to manage your watchlist, recommendations, and premium features.'),
            ]);
    }

    protected function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['boolean'],
        ];
    }

    protected function isRateLimited(): bool
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return false;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey());

        $this->addError('email', __('auth.throttle', [
            'seconds' => $seconds,
            'minutes' => (int) ceil($seconds / 60),
        ]));

        return true;
    }

    protected function throttleKey(): string
    {
        return Str::lower($this->email).'|'.request()->ip();
    }
}
