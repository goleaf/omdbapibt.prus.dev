<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class WelcomeUser extends Mailable implements ShouldQueue
{
    use Queueable;

    public function __construct(public User $user)
    {
        $this->onQueue('emails');
        $this->afterCommit();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('emails.welcome.subject', [
                'app' => config('app.name'),
                'name' => $this->user->name,
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.welcome-user',
            with: [
                'user' => $this->user,
            ],
        );
    }
}
