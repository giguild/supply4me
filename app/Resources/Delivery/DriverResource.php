<?php

namespace App\Resources\Delivery;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->when(isset($this->email), $this->email),
            'license_number' => $this->when(isset($this->license_number), $this->license_number),
            'vehicle_type' => $this->when(isset($this->vehicle_type), $this->vehicle_type),
            'vehicle_registration' => $this->when(isset($this->vehicle_registration), $this->vehicle_registration),
            'status' => $this->when(isset($this->status), $this->status),
            'status_label' => $this->when(isset($this->status), fn () => $this->status->label()),
            'notes' => $this->when(isset($this->notes), $this->notes),
            'active_deliveries_count' => $this->when(
                $this->relationLoaded('deliveries'),
                fn () => $this->deliveries->filter(fn ($d) => in_array($d->status, ['assigned', 'out_for_delivery']))->count()
            ),
            'deliveries_count' => $this->whenCounted('deliveries'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
