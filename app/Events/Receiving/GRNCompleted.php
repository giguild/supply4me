<?php

namespace App\Events\Receiving;

use App\Models\Receiving\GoodsReceivedNote;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GRNCompleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public GoodsReceivedNote $grn,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('company.'.$this->grn->company_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'grn.completed';
    }

    public function broadcastWith(): array
    {
        return [
            'grn' => [
                'id' => $this->grn->id,
                'grn_number' => $this->grn->grn_number,
                'status' => $this->grn->status->value,
                'received_date' => $this->grn->received_date->toDateString(),
            ],
        ];
    }
}
