<?php

namespace App\Events\PickingPacking;

use App\Models\PickingPacking\PickList;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PickListCompleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public PickList $pickList,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('company.'.$this->pickList->company_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'pick_list.completed';
    }

    public function broadcastWith(): array
    {
        return [
            'pick_list' => [
                'id' => $this->pickList->id,
                'pick_list_number' => $this->pickList->pick_list_number,
                'order_id' => $this->pickList->order_id,
                'status' => $this->pickList->status->value,
                'completed_at' => $this->pickList->completed_at?->toISOString(),
            ],
        ];
    }
}
