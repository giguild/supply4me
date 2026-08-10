<?php

namespace App\Events\Receiving;

use App\Models\Receiving\GoodsReceivedNote;
use App\Models\Receiving\GoodsReceivedNoteItem;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GoodsReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public GoodsReceivedNote $grn,
        public GoodsReceivedNoteItem $grnItem,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('company.'.$this->grn->company_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'grn.goods_received';
    }

    public function broadcastWith(): array
    {
        return [
            'grn' => [
                'id' => $this->grn->id,
                'grn_number' => $this->grn->grn_number,
            ],
            'grn_item' => [
                'id' => $this->grnItem->id,
                'product_id' => $this->grnItem->product_id,
                'quantity_ordered' => $this->grnItem->quantity_ordered,
                'quantity_received' => $this->grnItem->quantity_received,
            ],
        ];
    }
}
