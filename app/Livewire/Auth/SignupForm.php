<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Support\Auth\CreateUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class SignupForm extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public bool $terms = false;

    public function mount(): void
    {
        if (Auth::check()) {
            $this->redirectRoute('dashboard', ['locale' => $this->locale()]);
        }
    }

    public function submit(CreateUser $createUser): void
    {
        $validated = $this->validate(
            $this->rules(),
            $this->messages(),
            $this->attributes()
        );

        $user = $createUser->handle([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'preferred_locale' => $this->locale(),
        ]);

        Auth::login($user);

        session()->flash('status', __('Welcome aboard! Your OMDb Stream account is ready.'));

        $this->redirectRoute('dashboard', ['locale' => $this->locale()]);
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email:filter', 'max:255', 'unique:'.User::class.',email'],
            'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            'password_confirmation' => ['required', 'string'],
            'terms' => ['accepted'],
        ];
    }

    protected function messages(): array
    {
        return [
            'terms.accepted' => __('You must accept the terms of service.'),
        ];
    }

    protected function attributes(): array
    {
        return [
            'password_confirmation' => __('password confirmation'),
        ];
    }

    protected function locale(): string
    {
        return app()->getLocale() ?: config('translatable.fallback_locale', config('app.fallback_locale', 'en'));
    }

    public function render()
    {
        return view('livewire.auth.signup-form')
            ->layout('layouts.app', [
                'title' => __('Join OMDb Stream'),
                'header' => __('Create your account'),
                'subheader' => __('Stay in sync with parser drops, curated collections, and personalized recommendations.'),
            ]);
    }
}
