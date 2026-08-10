<?php

namespace App\Resources\Companies;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'name' => $this->name,
            'code' => $this->when(isset($this->code), $this->code),
            'address' => $this->when(isset($this->address), $this->address),
            'city' => $this->when(isset($this->city), $this->city),
            'state' => $this->when(isset($this->state), $this->state),
            'country' => $this->when(isset($this->country), $this->country),
            'postal_code' => $this->when(isset($this->postal_code), $this->postal_code),
            'phone' => $this->when(isset($this->phone), $this->phone),
            'email' => $this->when(isset($this->email), $this->email),
            'type' => $this->when(isset($this->type), $this->type),
            'is_default' => $this->when(isset($this->is_default), $this->is_default),
            'status' => $this->when(isset($this->status), $this->status),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
