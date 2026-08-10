<?php

namespace App\Events\Customers;

use App\Enums\Customers\CreditStatus;
use App\Models\Customers\Customer;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CreditStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Customer $customer,
        public CreditStatus $oldStatus,
        public CreditStatus $newStatus,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('company.'.$this->customer->company_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'customer.credit_status_changed';
    }

    public function broadcastWith(): array
    {
        return [
            'customer' => [
                'id' => $this->customer->id,
                'name' => $this->customer->name,
                'customer_number' => $this->customer->customer_number,
            ],
            'old_status' => $this->oldStatus->value,
            'new_status' => $this->newStatus->value,
        ];
    }
}
