<?php

namespace App\Resources\Inventory;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockAdjustmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'adjustment_number' => $this->adjustment_number,
            'warehouse_id' => $this->warehouse_id,
            'type' => $this->when(isset($this->type), $this->type),
            'status' => $this->when(isset($this->status), $this->status),
            'reason' => $this->when(isset($this->reason), $this->reason),
            'notes' => $this->when(isset($this->notes), $this->notes),
            'approved_at' => $this->when(isset($this->approved_at), $this->approved_at),
            'rejected_at' => $this->when(isset($this->rejected_at), $this->rejected_at),
            'rejection_reason' => $this->when(isset($this->rejection_reason), $this->rejection_reason),
            'items' => StockAdjustmentItemResource::collection($this->whenLoaded('items')),
            'warehouse' => new WarehouseResource($this->whenLoaded('warehouse')),
            'approved_by' => new \App\Resources\Core\UserResource($this->whenLoaded('approvedBy')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
