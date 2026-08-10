<?php

namespace App\Resources\Companies;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->when(isset($this->phone), $this->phone),
            'address' => $this->when(isset($this->address), $this->address),
            'city' => $this->when(isset($this->city), $this->city),
            'state' => $this->when(isset($this->state), $this->state),
            'country' => $this->when(isset($this->country), $this->country),
            'postal_code' => $this->when(isset($this->postal_code), $this->postal_code),
            'tax_number' => $this->when(isset($this->tax_number), $this->tax_number),
            'registration_number' => $this->when(isset($this->registration_number), $this->registration_number),
            'website' => $this->when(isset($this->website), $this->website),
            'logo' => $this->when(isset($this->logo), $this->logo),
            'status' => $this->when(isset($this->status), $this->status),
            'settings' => new CompanySettingResource($this->whenLoaded('settings')),
            'branches' => BranchResource::collection($this->whenLoaded('branches')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
