<?php

namespace App\Mail;

use App\Models\Entreprise;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewRegistrationNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Entreprise $entreprise) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Donnez Votre Sang] Nouvelle inscription — ' . $this->entreprise->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-registration',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
