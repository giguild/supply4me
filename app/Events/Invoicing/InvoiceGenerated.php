<?php

namespace App\Events\Invoicing;

use App\Models\Invoicing\Invoice;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InvoiceGenerated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Invoice $invoice,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('company.'.$this->invoice->company_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'invoice.generated';
    }

    public function broadcastWith(): array
    {
        return [
            'invoice' => [
                'id' => $this->invoice->id,
                'invoice_number' => $this->invoice->invoice_number,
                'total_amount' => $this->invoice->total_amount,
                'status' => $this->invoice->status->value,
            ],
        ];
    }
}
