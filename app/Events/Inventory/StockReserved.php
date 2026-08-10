<?php

namespace App\Events\Inventory;

use App\Models\Inventory\StockItem;
use App\Models\Orders\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StockReserved implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public StockItem $stockItem,
        public Order $order,
        public float $quantity,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('company.'.$this->stockItem->company_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'stock.reserved';
    }

    public function broadcastWith(): array
    {
        return [
            'stock_item' => [
                'id' => $this->stockItem->id,
                'quantity_on_hand' => $this->stockItem->quantity_on_hand,
                'quantity_reserved' => $this->stockItem->quantity_reserved,
            ],
            'order' => [
                'id' => $this->order->id,
                'order_number' => $this->order->order_number,
            ],
            'quantity' => $this->quantity,
        ];
    }
}
