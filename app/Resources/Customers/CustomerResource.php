<?php

namespace App\Resources\Customers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'email' => $this->email,
            'phone' => $this->when(isset($this->phone), $this->phone),
            'mobile' => $this->when(isset($this->mobile), $this->mobile),
            'type' => $this->when(isset($this->type), $this->type),
            'type_label' => $this->when(isset($this->type), fn () => $this->type->label()),
            'status' => $this->when(isset($this->status), $this->status),
            'address' => $this->when(isset($this->address), $this->address),
            'city' => $this->when(isset($this->city), $this->city),
            'state' => $this->when(isset($this->state), $this->state),
            'country' => $this->when(isset($this->country), $this->country),
            'postal_code' => $this->when(isset($this->postal_code), $this->postal_code),
            'tax_number' => $this->when(isset($this->tax_number), $this->tax_number),
            'credit_limit' => $this->when(isset($this->credit_limit), (float) $this->credit_limit),
            'credit_used' => $this->when(isset($this->credit_used), (float) $this->credit_used),
            'credit_available' => $this->when(isset($this->credit_limit) && isset($this->credit_used), (float) ($this->credit_limit - $this->credit_used)),
            'credit_status' => $this->when(isset($this->credit_status), $this->credit_status),
            'payment_terms' => $this->when(isset($this->payment_terms), $this->payment_terms),
            'discount_percentage' => $this->when(isset($this->discount_percentage), (float) $this->discount_percentage),
            'contacts_count' => $this->whenCounted('contacts'),
            'orders_count' => $this->whenCounted('orders'),
            'contacts' => CustomerContactResource::collection($this->whenLoaded('contacts')),
            'addresses' => CustomerAddressResource::collection($this->whenLoaded('addresses')),
            'sales_rep' => new \App\Resources\Core\UserResource($this->whenLoaded('salesRep')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
