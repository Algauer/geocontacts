<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountDeletedMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public string $name,
        public string $email,
        public string $restoreUrl
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Sua conta foi excluida temporariamente'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.account-deleted'
        );
    }
}
