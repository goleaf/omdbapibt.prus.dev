<?php

namespace App\Livewire\Auth;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class SignupForm extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function mount(): void
    {
        if (Auth::check()) {
            $this->redirect(
                route('dashboard', ['locale' => app()->getLocale()]),
                navigate: true
            );
        }
    }

    public function register(): void
    {
        if (Auth::check()) {
            $this->redirect(
                route('dashboard', ['locale' => app()->getLocale()]),
                navigate: true
            );

            return;
        }

        $validated = $this->validate($this->rules());

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => UserRole::User,
            'preferred_locale' => app()->getLocale(),
        ]);

        event(new Registered($user));

        Auth::login($user);

        session()->regenerate();

        session()->flash('status', __('Welcome aboard! Your account is ready.'));

        $this->reset('password', 'password_confirmation');

        $this->redirect(
            route('dashboard', ['locale' => app()->getLocale()]),
            navigate: true
        );
    }

    public function render(): View
    {
        return view('livewire.auth.signup-form')
            ->layout('layouts.app', [
                'title' => __('Join OMDb Stream'),
                'header' => __('Create your account'),
                'subheader' => __('Stay in sync with parser drops, curated collections, and personalized recommendations.'),
            ]);
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            'password_confirmation' => ['required', 'string'],
        ];
    }
}
