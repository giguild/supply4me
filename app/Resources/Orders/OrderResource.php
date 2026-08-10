<?php

namespace App\Resources\Orders;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'customer_id' => $this->customer_id,
            'status' => $this->when(isset($this->status), $this->status),
            'status_label' => $this->when(isset($this->status), fn () => $this->status->label()),
            'type' => $this->when(isset($this->type), $this->type),
            'priority' => $this->when(isset($this->priority), $this->priority),
            'payment_status' => $this->when(isset($this->payment_status), $this->payment_status),
            'fulfillment_status' => $this->when(isset($this->fulfillment_status), $this->fulfillment_status),
            'subtotal' => (float) $this->subtotal,
            'discount_amount' => $this->when(isset($this->discount_amount), (float) $this->discount_amount),
            'tax_amount' => $this->when(isset($this->tax_amount), (float) $this->tax_amount),
            'shipping_amount' => $this->when(isset($this->shipping_amount), (float) $this->shipping_amount),
            'total_amount' => (float) $this->total_amount,
            'currency' => $this->when(isset($this->currency), $this->currency),
            'shipping_address' => $this->when(isset($this->shipping_address), $this->shipping_address),
            'expected_delivery_date' => $this->when(isset($this->expected_delivery_date), $this->expected_delivery_date),
            'notes' => $this->when(isset($this->notes), $this->notes),
            'customer' => new \App\Resources\Customers\CustomerResource($this->whenLoaded('customer')),
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'status_history' => OrderStatusHistoryResource::collection($this->whenLoaded('statusHistory')),
            'payments' => \App\Resources\Payments\PaymentResource::collection($this->whenLoaded('payments')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
