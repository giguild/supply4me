<?php

namespace App\Events\Shipping;

use App\Models\Shipping\Shipment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ShipmentCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Shipment $shipment,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('company.'.$this->shipment->company_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'shipment.created';
    }

    public function broadcastWith(): array
    {
        return [
            'shipment' => [
                'id' => $this->shipment->id,
                'shipment_number' => $this->shipment->shipment_number,
                'order_id' => $this->shipment->order_id,
                'status' => $this->shipment->status->value,
            ],
        ];
    }
}
