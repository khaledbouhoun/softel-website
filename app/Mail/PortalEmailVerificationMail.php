<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class PortalEmailVerificationMail extends Mailable
{
    use Queueable;

    public function __construct(
        public readonly string $name,
        public readonly string $code,
        public readonly string $mailLocale,
        public readonly string $email,
    ) {
        $this->locale($mailLocale);
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: __('site.portal_verify_email_subject'));
    }

    public function content(): Content
    {
        return new Content(view: 'emails.portal-verify');
    }
}
