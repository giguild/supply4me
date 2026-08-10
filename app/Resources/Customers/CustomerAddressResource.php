<?php

namespace App\Resources\Customers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerAddressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'label' => $this->when(isset($this->label), $this->label),
            'address_line_1' => $this->address_line_1,
            'address_line_2' => $this->when(isset($this->address_line_2), $this->address_line_2),
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'postal_code' => $this->postal_code,
            'phone' => $this->when(isset($this->phone), $this->phone),
            'is_default' => $this->when(isset($this->is_default), $this->is_default),
            'delivery_instructions' => $this->when(isset($this->delivery_instructions), $this->delivery_instructions),
            'latitude' => $this->when(isset($this->latitude), $this->latitude),
            'longitude' => $this->when(isset($this->longitude), $this->longitude),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
