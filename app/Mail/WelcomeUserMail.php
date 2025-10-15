<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeUserMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(public readonly User $user)
    {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Welcome to OMDb Stream'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.auth.welcome',
            with: [
                'user' => $this->user,
            ],
        );
    }

    /**
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
