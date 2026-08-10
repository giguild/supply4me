<?php

namespace App\Events\Orders;

use App\Enums\Orders\OrderStatus;
use App\Models\Core\User;
use App\Models\Orders\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Order $order,
        public OrderStatus $oldStatus,
        public OrderStatus $newStatus,
        public User $user,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('company.'.$this->order->company_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'order.status_changed';
    }

    public function broadcastWith(): array
    {
        return [
            'order' => [
                'id' => $this->order->id,
                'order_number' => $this->order->order_number,
            ],
            'old_status' => $this->oldStatus->value,
            'new_status' => $this->newStatus->value,
            'changed_by' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
        ];
    }
}
