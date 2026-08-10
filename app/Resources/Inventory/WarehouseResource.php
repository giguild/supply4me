<?php

namespace App\Resources\Inventory;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->when(isset($this->code), $this->code),
            'address' => $this->when(isset($this->address), $this->address),
            'city' => $this->when(isset($this->city), $this->city),
            'state' => $this->when(isset($this->state), $this->state),
            'country' => $this->when(isset($this->country), $this->country),
            'phone' => $this->when(isset($this->phone), $this->phone),
            'email' => $this->when(isset($this->email), $this->email),
            'is_active' => $this->when(isset($this->is_active), $this->is_active),
            'is_default' => $this->when(isset($this->is_default), $this->is_default),
        ];
    }
}
