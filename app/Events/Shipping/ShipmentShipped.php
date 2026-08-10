<?php

namespace App\Events\Shipping;

use App\Models\Shipping\Shipment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ShipmentShipped implements ShouldBroadcast
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
        return 'shipment.shipped';
    }

    public function broadcastWith(): array
    {
        return [
            'shipment' => [
                'id' => $this->shipment->id,
                'shipment_number' => $this->shipment->shipment_number,
                'tracking_number' => $this->shipment->tracking_number,
                'estimated_delivery_date' => $this->shipment->estimated_delivery_date?->toDateString(),
                'status' => $this->shipment->status->value,
            ],
        ];
    }
}
