<?php

namespace App\Events\PickingPacking;

use App\Models\PickingPacking\PackingList;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderPacked implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public PackingList $packingList,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('company.'.$this->packingList->company_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'order.packed';
    }

    public function broadcastWith(): array
    {
        return [
            'packing_list' => [
                'id' => $this->packingList->id,
                'packing_list_number' => $this->packingList->packing_list_number,
                'order_id' => $this->packingList->order_id,
                'status' => $this->packingList->status->value,
            ],
        ];
    }
}
