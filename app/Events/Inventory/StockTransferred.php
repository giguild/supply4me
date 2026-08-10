<?php

namespace App\Events\Inventory;

use App\Models\Core\User;
use App\Models\Inventory\StockTransfer;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StockTransferred implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public StockTransfer $stockTransfer,
        public User $user,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('company.'.$this->stockTransfer->company_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'stock.transferred';
    }

    public function broadcastWith(): array
    {
        return [
            'stock_transfer' => [
                'id' => $this->stockTransfer->id,
                'transfer_number' => $this->stockTransfer->transfer_number,
                'from_warehouse_id' => $this->stockTransfer->from_warehouse_id,
                'to_warehouse_id' => $this->stockTransfer->to_warehouse_id,
                'status' => $this->stockTransfer->status->value,
            ],
            'performed_by' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
        ];
    }
}
