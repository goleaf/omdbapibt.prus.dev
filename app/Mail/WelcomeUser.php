<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeUser extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(public readonly User $user)
    {
    }

    public function build(): self
    {
        return $this
            ->subject(__('mail.welcome.subject', ['name' => $this->user->name]))
            ->view('mail.welcome-user')
            ->with([
                'user' => $this->user,
            ]);
    }
}
