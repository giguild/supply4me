<?php

namespace App\Mail;

use App\Models\Core\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeUserMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public ?string $password = null,
        public array $roleNames = [],
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to SUPPLY4ME',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.core.welcome',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}