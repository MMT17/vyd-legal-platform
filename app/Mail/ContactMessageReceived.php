<?php

namespace App\Mail;

use App\Models\Cms\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Route;

class ContactMessageReceived extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public ContactMessage $contactMessage,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [
                new Address($this->contactMessage->email, $this->contactMessage->name),
            ],
            subject: 'Nuevo mensaje de contacto'.($this->contactMessage->subject ? ': '.$this->contactMessage->subject : ''),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-message-received',
            with: [
                'adminUrl' => $this->adminUrl(),
            ],
        );
    }

    private function adminUrl(): ?string
    {
        if (! Route::has('filament.admin.resources.contact-messages.view')) {
            return null;
        }

        return route('filament.admin.resources.contact-messages.view', [
            'record' => $this->contactMessage,
        ]);
    }
}
