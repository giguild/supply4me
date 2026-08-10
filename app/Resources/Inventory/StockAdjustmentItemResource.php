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
            'stock_adjustment_id' => $this->stock_adjustment_id,
            'product_id' => $this->product_id,
            'quantity' => $this->quantity,
            'unit_cost' => $this->when(isset($this->unit_cost), (float) $this->unit_cost),
            'notes' => $this->when(isset($this->notes), $this->notes),
            'product' => new \App\Resources\Products\ProductResource($this->whenLoaded('product')),
            'created_at' => $this->created_at,
        ];
    }
}
