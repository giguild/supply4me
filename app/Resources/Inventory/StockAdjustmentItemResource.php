<?php

namespace App\Resources\Inventory;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockAdjustmentItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'adjustment_id' => $this->adjustment_id,
            'product_id' => $this->product_id,
            'variant_id' => $this->variant_id,
            'quantity_before' => (float) $this->quantity_before,
            'quantity_after' => (float) $this->quantity_after,
            'difference' => (float) $this->difference,
            'unit_cost' => $this->when(isset($this->unit_cost), (float) $this->unit_cost),
            'notes' => $this->when(isset($this->notes), $this->notes),
            'product' => new \App\Resources\Products\ProductResource($this->whenLoaded('product')),
            'created_at' => $this->created_at,
        ];
    }
}