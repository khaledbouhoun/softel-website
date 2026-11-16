<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class PortalPasswordResetMail extends Mailable
{
    use Queueable;

    public function __construct(public readonly string $resetUrl) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: __('site.portal_reset_mail_subject'));
    }

    public function content(): Content
    {
        return new Content(view: 'emails.portal-reset');
    }
}
