<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApprovalMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function envelope(): Envelope
    {
        $roleLabel = $this->user->hasRole('ogretmen') ? 'Öğretmen' : 'Veli';
        return new Envelope(
            subject: "{$roleLabel} Paneliniz Aktifleştirildi",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.approval',
        );
    }
}
