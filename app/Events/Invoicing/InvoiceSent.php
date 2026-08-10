<?php

namespace App\Events\Invoicing;

use App\Models\Core\User;
use App\Models\Invoicing\Invoice;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InvoiceSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Invoice $invoice,
        public User $user,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('company.'.$this->invoice->company_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'invoice.sent';
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
            'sent_by' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
        ];
    }
}
