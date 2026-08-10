<?php

namespace App\Resources\Orders;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'product_id' => $this->product_id,
            'variant_id' => $this->when(isset($this->variant_id), $this->variant_id),
            'quantity' => $this->quantity,
            'quantity_picked' => $this->when(isset($this->quantity_picked), $this->quantity_picked),
            'quantity_packed' => $this->when(isset($this->quantity_packed), $this->quantity_packed),
            'quantity_shipped' => $this->when(isset($this->quantity_shipped), $this->quantity_shipped),
            'quantity_delivered' => $this->when(isset($this->quantity_delivered), $this->quantity_delivered),
            'unit_price' => (float) $this->unit_price,
            'discount' => $this->when(isset($this->discount), (float) $this->discount),
            'tax_rate' => $this->when(isset($this->tax_rate), (float) $this->tax_rate),
            'tax_amount' => $this->when(isset($this->tax_amount), (float) $this->tax_amount),
            'line_total' => (float) $this->line_total,
            'notes' => $this->when(isset($this->notes), $this->notes),
            'product' => new \App\Resources\Products\ProductResource($this->whenLoaded('product')),
            'variant' => new \App\Resources\Products\ProductVariantResource($this->whenLoaded('variant')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
