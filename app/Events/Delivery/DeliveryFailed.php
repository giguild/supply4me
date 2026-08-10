<?php

namespace App\Events\Delivery;

use App\Models\Delivery\Delivery;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeliveryFailed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Delivery $delivery,
        public ?string $reason = null,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('company.'.$this->delivery->company_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'delivery.failed';
    }

    public function broadcastWith(): array
    {
        return [
            'delivery' => [
                'id' => $this->delivery->id,
                'delivery_number' => $this->delivery->delivery_number,
                'order_id' => $this->delivery->order_id,
                'status' => $this->delivery->status->value,
            ],
            'reason' => $this->reason,
        ];
    }
}
