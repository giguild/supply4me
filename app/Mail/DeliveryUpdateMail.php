<?php

namespace App\Mail;

use App\Models\Delivery\Delivery;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DeliveryUpdateMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Delivery $delivery,
        public string $statusText,
        public ?string $failureReason = null,
    ) {
        $this->delivery->load(['customer', 'order']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Delivery Update ' . $this->delivery->delivery_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.deliveries.delivery-update',
            with: [
                'statusText' => $this->statusText,
                'failureReason' => $this->failureReason,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}