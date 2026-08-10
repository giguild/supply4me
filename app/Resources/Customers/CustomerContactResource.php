<?php

namespace App\Resources\Customers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerContactResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'name' => $this->name,
            'email' => $this->when(isset($this->email), $this->email),
            'phone' => $this->when(isset($this->phone), $this->phone),
            'mobile' => $this->when(isset($this->mobile), $this->mobile),
            'position' => $this->when(isset($this->position), $this->position),
            'is_primary' => $this->when(isset($this->is_primary), $this->is_primary),
            'notes' => $this->when(isset($this->notes), $this->notes),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
