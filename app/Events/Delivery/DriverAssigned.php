<?php

namespace App\Events\Delivery;

use App\Models\Delivery\Delivery;
use App\Models\Delivery\Driver;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DriverAssigned implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Delivery $delivery,
        public Driver $driver,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('company.'.$this->delivery->company_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'delivery.driver_assigned';
    }

    public function broadcastWith(): array
    {
        return [
            'delivery' => [
                'id' => $this->delivery->id,
                'delivery_number' => $this->delivery->delivery_number,
                'order_id' => $this->delivery->order_id,
                'scheduled_date' => $this->delivery->scheduled_date?->toDateString(),
            ],
            'driver' => [
                'id' => $this->driver->id,
                'user_id' => $this->driver->user_id,
                'vehicle_type' => $this->driver->vehicle_type,
            ],
        ];
    }
}
