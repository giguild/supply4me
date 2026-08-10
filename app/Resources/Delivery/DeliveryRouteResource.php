<?php

namespace App\Resources\Delivery;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryRouteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'route_number' => $this->route_number,
            'driver_id' => $this->driver_id,
            'date' => $this->when(isset($this->date), $this->date),
            'status' => $this->when(isset($this->status), $this->status),
            'status_label' => $this->when(isset($this->status), fn () => $this->status->label()),
            'notes' => $this->when(isset($this->notes), $this->notes),
            'started_at' => $this->when(isset($this->started_at), $this->started_at),
            'completed_at' => $this->when(isset($this->completed_at), $this->completed_at),
            'total_stops' => $this->when(
                $this->relationLoaded('stops'),
                fn () => $this->stops->count()
            ),
            'completed_stops' => $this->when(
                $this->relationLoaded('stops'),
                fn () => $this->stops->filter(fn ($s) => $s->status === 'delivered')->count()
            ),
            'driver' => new DriverResource($this->whenLoaded('driver')),
            'deliveries' => DeliveryResource::collection($this->whenLoaded('deliveries')),
            'stops' => DeliveryRouteStopResource::collection($this->whenLoaded('stops')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
