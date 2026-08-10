<?php

namespace App\Resources\Delivery;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'delivery_number' => $this->delivery_number,
            'order_id' => $this->order_id,
            'driver_id' => $this->when(isset($this->driver_id), $this->driver_id),
            'route_id' => $this->when(isset($this->route_id), $this->route_id),
            'status' => $this->when(isset($this->status), $this->status),
            'status_label' => $this->when(isset($this->status), fn () => $this->status->label()),
            'scheduled_date' => $this->when(isset($this->scheduled_date), $this->scheduled_date),
            'scheduled_time_start' => $this->when(isset($this->scheduled_time_start), $this->scheduled_time_start),
            'scheduled_time_end' => $this->when(isset($this->scheduled_time_end), $this->scheduled_time_end),
            'delivery_address' => $this->when(isset($this->delivery_address), $this->delivery_address),
            'contact_name' => $this->when(isset($this->contact_name), $this->contact_name),
            'contact_phone' => $this->when(isset($this->contact_phone), $this->contact_phone),
            'priority' => $this->when(isset($this->priority), $this->priority),
            'notes' => $this->when(isset($this->notes), $this->notes),
            'started_at' => $this->when(isset($this->started_at), $this->started_at),
            'completed_at' => $this->when(isset($this->completed_at), $this->completed_at),
            'proof_of_delivery' => $this->when(isset($this->proof_of_delivery), $this->proof_of_delivery),
            'recipient_name' => $this->when(isset($this->recipient_name), $this->recipient_name),
            'signature' => $this->when(isset($this->signature), $this->signature),
            'failed_reason' => $this->when(isset($this->failed_reason), $this->failed_reason),
            'failed_condition' => $this->when(isset($this->failed_condition), $this->failed_condition),
            'attempt_count' => $this->when(isset($this->attempt_count), $this->attempt_count),
            'driver' => new DriverResource($this->whenLoaded('driver')),
            'order' => new \App\Resources\Orders\OrderResource($this->whenLoaded('order')),
            'items' => DeliveryItemResource::collection($this->whenLoaded('items')),
            'route' => new DeliveryRouteResource($this->whenLoaded('route')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
