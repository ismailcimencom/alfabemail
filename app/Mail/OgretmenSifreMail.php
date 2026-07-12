<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OgretmenSifreMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $token;
    public string $email;

    public function __construct(string $token, string $email)
    {
        $this->token = $token;
        $this->email = $email;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Öğretmen Hesabınız Oluşturuldu - Şifre Belirleme',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.ogretmen-sifre',
        );
    }
}
