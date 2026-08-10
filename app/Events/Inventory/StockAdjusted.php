<?php

namespace App\Events\Inventory;

use App\Models\Core\User;
use App\Models\Inventory\StockAdjustment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StockAdjusted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public StockAdjustment $stockAdjustment,
        public User $user,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('company.'.$this->stockAdjustment->company_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'stock.adjusted';
    }

    public function broadcastWith(): array
    {
        return [
            'stock_adjustment' => [
                'id' => $this->stockAdjustment->id,
                'adjustment_number' => $this->stockAdjustment->adjustment_number,
                'type' => $this->stockAdjustment->type->value,
                'reason' => $this->stockAdjustment->reason,
            ],
            'performed_by' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
        ];
    }
}
