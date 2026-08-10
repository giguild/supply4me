<?php

namespace App\Events\Delivery;

use App\Models\Delivery\Delivery;
use Carbon\Carbon;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeliveryRescheduled implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Delivery $delivery,
        public Carbon $newDate,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('company.'.$this->delivery->company_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'delivery.rescheduled';
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
            'new_date' => $this->newDate->toDateString(),
        ];
    }
}
