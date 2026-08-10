<?php

namespace App\Resources\Inventory;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockMovementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'stock_item_id' => $this->stock_item_id,
            'type' => $this->when(isset($this->type), $this->type),
            'quantity' => $this->quantity,
            'quantity_before' => $this->when(isset($this->quantity_before), $this->quantity_before),
            'quantity_after' => $this->when(isset($this->quantity_after), $this->quantity_after),
            'reference_type' => $this->when(isset($this->reference_type), $this->reference_type),
            'reference_id' => $this->when(isset($this->reference_id), $this->reference_id),
            'notes' => $this->when(isset($this->notes), $this->notes),
            'unit_cost' => $this->when(isset($this->unit_cost), (float) $this->unit_cost),
            'stock_item' => new StockItemResource($this->whenLoaded('stockItem')),
            'created_by' => $this->when(isset($this->created_by), $this->created_by),
            'created_at' => $this->created_at,
        ];
    }
}
