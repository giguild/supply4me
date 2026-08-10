<?php

namespace App\Events\Orders;

use App\Models\Core\User;
use App\Models\Orders\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCancelled implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Order $order,
        public User $user,
        public ?string $reason = null,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('company.'.$this->order->company_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'order.cancelled';
    }

    public function broadcastWith(): array
    {
        return [
            'order' => [
                'id' => $this->order->id,
                'order_number' => $this->order->order_number,
                'status' => $this->order->status->value,
            ],
            'cancelled_by' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
            'reason' => $this->reason,
        ];
    }
}
