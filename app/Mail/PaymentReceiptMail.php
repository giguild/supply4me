<?php

namespace App\Mail;

use App\Models\Payments\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Payment $payment,
    ) {
        $this->payment->load(['customer', 'allocations.invoice', 'company', 'supplier']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment Receipt ' . $this->payment->payment_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payments.payment-receipt',
        );
    }

    public function attachments(): array
    {
        $payment = $this->payment->load(['customer', 'supplier', 'allocations.invoice', 'approvedBy', 'receivedBy', 'company', 'branch']);
        $pdf = Pdf::loadView('payments.receipt', ['payment' => $payment])->output();

        return [
            Attachment::fromData(
                fn () => $pdf,
                preg_replace('/[^A-Za-z0-9\-_.]/', '-', $payment->payment_number) . '.pdf'
            )->withMime('application/pdf'),
        ];
    }
}