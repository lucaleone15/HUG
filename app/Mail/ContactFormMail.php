<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;

class ContactFormMail extends Mailable
{
    private const TYPE_LABELS = [
        'type_general'     => 'Question générale',
        'type_partnership' => 'Partenariat',
        'type_technical'   => 'Support technique',
        'type_other'       => 'Autre',
    ];

    public function __construct(
        public string $senderName,
        public string $senderEmail,
        public string $type,
        public string $userMessage,
    ) {
        $this->type = self::TYPE_LABELS[$type] ?? $type;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[Contact] {$this->type} — {$this->senderName}",
            replyTo: [new Address($this->senderEmail, $this->senderName)],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-form',
        );
    }
}
