<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Services\Users\CreateUser;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class SignupForm extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    protected CreateUser $createUser;

    public function boot(CreateUser $createUser): void
    {
        $this->createUser = $createUser;
    }

    public function register(): void
    {
        $validated = $this->validate($this->rules());

        $user = $this->createUser->handle([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'preferred_locale' => app()->getLocale(),
        ]);

        Auth::login($user);
        session()->regenerate();

        $this->reset('password');

        $this->redirectRoute('dashboard', ['locale' => app()->getLocale()], navigate: true);
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
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class, 'email'),
            ],
            'password' => ['required', 'string', 'max:255', Password::min(8)],
        ];
    }
}
