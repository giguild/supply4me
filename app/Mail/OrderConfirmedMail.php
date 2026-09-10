<?php

namespace App\Mail;

use App\Models\Orders\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
    ) {
        $this->order->load(['customer', 'items', 'invoice']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Order ' . $this->order->order_number . ' Confirmed',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.order-confirmed',
            with: [
                'invoice' => $this->order->invoice,
            ],
        );
    }

    public function attachments(): array
    {
        if (!$this->order->invoice) {
            return [];
        }

        $invoice = $this->order->invoice->load(['customer', 'items.product', 'payments', 'createdBy', 'company']);
        $pdf = Pdf::loadView('invoices.print', ['invoice' => $invoice])->output();

        return [
            Attachment::fromData(
                fn () => $pdf,
                preg_replace('/[^A-Za-z0-9\-_.]/', '-', $invoice->invoice_number) . '.pdf'
            )->withMime('application/pdf'),
        ];
    }
}