<?php

namespace App\Events\Invoicing;

use App\Models\Invoicing\Invoice;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InvoiceOverdue implements ShouldBroadcast
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
        return 'invoice.overdue';
    }

    public function broadcastWith(): array
    {
        return [
            'invoice' => [
                'id' => $this->invoice->id,
                'invoice_number' => $this->invoice->invoice_number,
                'total_amount' => $this->invoice->total_amount,
                'balance_due' => $this->invoice->balance_due,
                'due_date' => $this->invoice->due_date->toDateString(),
                'status' => $this->invoice->status->value,
            ],
        ];
    }
}
