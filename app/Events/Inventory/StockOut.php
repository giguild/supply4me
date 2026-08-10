<?php

namespace App\Events\Inventory;

use App\Models\Inventory\StockItem;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StockOut implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public StockItem $stockItem,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('company.'.$this->stockItem->company_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'stock.out';
    }

    public function broadcastWith(): array
    {
        return [
            'stock_item' => [
                'id' => $this->stockItem->id,
                'quantity_on_hand' => $this->stockItem->quantity_on_hand,
                'reorder_level' => $this->stockItem->reorder_level,
            ],
        ];
    }
}
