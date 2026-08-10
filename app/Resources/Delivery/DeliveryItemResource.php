<?php

namespace App\Resources\Delivery;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'delivery_id' => $this->delivery_id,
            'order_item_id' => $this->when(isset($this->order_item_id), $this->order_item_id),
            'product_id' => $this->product_id,
            'quantity' => $this->quantity,
            'quantity_delivered' => $this->when(isset($this->quantity_delivered), $this->quantity_delivered),
            'condition' => $this->when(isset($this->condition), $this->condition),
            'notes' => $this->when(isset($this->notes), $this->notes),
            'product' => new \App\Resources\Products\ProductResource($this->whenLoaded('product')),
            'created_at' => $this->created_at,
        ];
    }
}
