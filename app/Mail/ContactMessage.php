<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ContactMessage extends Mailable
{
    public function __construct(
        public string $nome,
        public string $email,
        public string $assunto,
        public string $mensagem,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: str_replace(["\r", "\n"], '', $this->assunto),
            replyTo: [new Address($this->email, str_replace(["\r", "\n"], '', $this->nome))],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.contact-message',
        );
    }
}
