<?php

namespace App\Mail;

use App\Models\Invoicing\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Invoice $invoice,
    ) {
        $this->invoice->load(['customer', 'items', 'company']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invoice ' . $this->invoice->invoice_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invoices.invoice',
        );
    }

    public function attachments(): array
    {
        $invoice = $this->invoice->load(['items.product', 'payments', 'createdBy']);
        $pdf = Pdf::loadView('invoices.print', ['invoice' => $invoice])->output();

        return [
            Attachment::fromData(
                fn () => $pdf,
                preg_replace('/[^A-Za-z0-9\-_.]/', '-', $invoice->invoice_number) . '.pdf'
            )->withMime('application/pdf'),
        ];
    }
}