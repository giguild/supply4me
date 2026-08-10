<?php

namespace App\Events\Orders;

use App\Models\Orders\Order;
use App\Models\Orders\OrderItem;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderItemRemoved implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Order $order,
        public OrderItem $orderItem,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('company.'.$this->order->company_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'order.item_removed';
    }

    public function broadcastWith(): array
    {
        return [
            'order' => [
                'id' => $this->order->id,
                'order_number' => $this->order->order_number,
            ],
            'order_item' => [
                'id' => $this->orderItem->id,
                'product_id' => $this->orderItem->product_id,
            ],
        ];
    }
}
