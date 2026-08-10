<?php

namespace App\Events\Shipping;

use App\Models\Shipping\Shipment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ShipmentDelivered implements ShouldBroadcast
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
        return 'shipment.delivered';
    }

    public function broadcastWith(): array
    {
        return [
            'shipment' => [
                'id' => $this->shipment->id,
                'shipment_number' => $this->shipment->shipment_number,
                'tracking_number' => $this->shipment->tracking_number,
                'actual_delivery_date' => $this->shipment->actual_delivery_date?->toDateString(),
                'delivered_at' => $this->shipment->delivered_at?->toISOString(),
                'status' => $this->shipment->status->value,
            ],
        ];
    }
}
