<?php

namespace App\Resources\Delivery;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryRouteStopResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'route_id' => $this->route_id,
            'delivery_id' => $this->delivery_id,
            'sequence' => $this->when(isset($this->sequence), $this->sequence),
            'status' => $this->when(isset($this->status), $this->status),
            'notes' => $this->when(isset($this->notes), $this->notes),
            'actual_arrival' => $this->when(isset($this->actual_arrival), $this->actual_arrival),
            'actual_departure' => $this->when(isset($this->actual_departure), $this->actual_departure),
            'proof_of_delivery' => $this->when(isset($this->proof_of_delivery), $this->proof_of_delivery),
            'delivery' => new DeliveryResource($this->whenLoaded('delivery')),
            'created_at' => $this->created_at,
        ];
    }
}
