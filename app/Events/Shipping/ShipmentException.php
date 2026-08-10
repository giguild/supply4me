<?php

namespace App\Events\Shipping;

use App\Models\Shipping\Shipment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ShipmentException implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Shipment $shipment,
        public ?string $reason = null,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('company.'.$this->shipment->company_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'shipment.exception';
    }

    public function broadcastWith(): array
    {
        return [
            'shipment' => [
                'id' => $this->shipment->id,
                'shipment_number' => $this->shipment->shipment_number,
                'tracking_number' => $this->shipment->tracking_number,
                'status' => $this->shipment->status->value,
            ],
            'reason' => $this->reason,
        ];
    }
}
