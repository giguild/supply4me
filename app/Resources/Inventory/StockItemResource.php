<?php

namespace App\Resources\Inventory;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'warehouse_id' => $this->warehouse_id,
            'quantity_on_hand' => $this->quantity_on_hand,
            'quantity_reserved' => $this->when(isset($this->quantity_reserved), $this->quantity_reserved),
            'quantity_available' => $this->when(
                isset($this->quantity_on_hand) && isset($this->quantity_reserved),
                fn () => $this->quantity_on_hand - ($this->quantity_reserved ?? 0)
            ),
            'quantity_on_order' => $this->when(isset($this->quantity_on_order), $this->quantity_on_order),
            'minimum_stock_level' => $this->when(isset($this->minimum_stock_level), $this->minimum_stock_level),
            'maximum_stock_level' => $this->when(isset($this->maximum_stock_level), $this->maximum_stock_level),
            'reorder_point' => $this->when(isset($this->reorder_point), $this->reorder_point),
            'status' => $this->when(isset($this->status), $this->status),
            'bin_location' => $this->when(isset($this->bin_location), $this->bin_location),
            'last_counted_at' => $this->when(isset($this->last_counted_at), $this->last_counted_at),
            'last_received_at' => $this->when(isset($this->last_received_at), $this->last_received_at),
            'product' => new \App\Resources\Products\ProductResource($this->whenLoaded('product')),
            'warehouse' => new WarehouseResource($this->whenLoaded('warehouse')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
