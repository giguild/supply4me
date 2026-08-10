<?php

namespace App\Events\Delivery;

use App\Models\Delivery\Delivery;
use App\Models\Delivery\DeliveryItem;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeliveryCompleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Delivery $delivery,
        public array $deliveryItems,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('company.'.$this->delivery->company_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'delivery.completed';
    }

    public function broadcastWith(): array
    {
        return [
            'delivery' => [
                'id' => $this->delivery->id,
                'delivery_number' => $this->delivery->delivery_number,
                'order_id' => $this->delivery->order_id,
                'actual_delivery_date' => $this->delivery->actual_delivery_date?->toDateString(),
                'status' => $this->delivery->status->value,
            ],
            'items' => collect($this->deliveryItems)->map(fn (DeliveryItem $item) => [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'quantity_delivered' => $item->quantity_delivered,
            ])->toArray(),
        ];
    }
}
