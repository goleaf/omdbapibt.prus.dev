<?php

namespace App\Livewire\Auth;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Password;
use Livewire\Component;

class ForgotPasswordForm extends Component
{
    public string $email = '';

    public ?string $statusMessage = null;

    public function sendResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'email'],
        ]);

        $this->statusMessage = null;

        $status = Password::sendResetLink(['email' => $this->email]);

        if ($status === Password::RESET_LINK_SENT) {
            $this->statusMessage = __($status);
            $this->resetErrorBag();

            return;
        }

        $this->addError('email', __($status));
    }

    public function render(): View
    {
        return view('livewire.auth.forgot-password-form')
            ->layout('layouts.app', [
                'title' => __('Reset your password'),
                'header' => __('Reset your password'),
                'subheader' => __('We will email you a secure link to create a new password.'),
            ]);
    }
}
