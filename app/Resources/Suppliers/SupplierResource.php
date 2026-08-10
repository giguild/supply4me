<?php

namespace App\Resources\Suppliers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'email' => $this->email,
            'phone' => $this->when(isset($this->phone), $this->phone),
            'address' => $this->when(isset($this->address), $this->address),
            'city' => $this->when(isset($this->city), $this->city),
            'state' => $this->when(isset($this->state), $this->state),
            'country' => $this->when(isset($this->country), $this->country),
            'postal_code' => $this->when(isset($this->postal_code), $this->postal_code),
            'tax_number' => $this->when(isset($this->tax_number), $this->tax_number),
            'payment_terms' => $this->when(isset($this->payment_terms), $this->payment_terms),
            'lead_time_days' => $this->when(isset($this->lead_time_days), $this->lead_time_days),
            'minimum_order_value' => $this->when(isset($this->minimum_order_value), (float) $this->minimum_order_value),
            'contact_person' => $this->when(isset($this->contact_person), $this->contact_person),
            'website' => $this->when(isset($this->website), $this->website),
            'status' => $this->when(isset($this->status), $this->status),
            'notes' => $this->when(isset($this->notes), $this->notes),
            'products_count' => $this->whenCounted('products'),
            'products' => \App\Resources\Products\ProductResource::collection($this->whenLoaded('products')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
