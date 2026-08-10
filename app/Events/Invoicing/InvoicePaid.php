<?php

namespace App\Events\Invoicing;

use App\Models\Invoicing\Invoice;
use App\Models\Payments\Payment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InvoicePaid implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Invoice $invoice,
        public Payment $payment,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('company.'.$this->invoice->company_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'invoice.paid';
    }

    public function broadcastWith(): array
    {
        return [
            'invoice' => [
                'id' => $this->invoice->id,
                'invoice_number' => $this->invoice->invoice_number,
                'total_amount' => $this->invoice->total_amount,
                'amount_paid' => $this->invoice->amount_paid,
                'status' => $this->invoice->status->value,
            ],
            'payment' => [
                'id' => $this->payment->id,
                'payment_number' => $this->payment->payment_number,
                'amount' => $this->payment->amount,
            ],
        ];
    }
}
