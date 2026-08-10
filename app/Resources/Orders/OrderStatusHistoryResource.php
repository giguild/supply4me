<?php

namespace App\Resources\Orders;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderStatusHistoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'status' => $this->status,
            'previous_status' => $this->when(isset($this->previous_status), $this->previous_status),
            'notes' => $this->when(isset($this->notes), $this->notes),
            'changed_by' => $this->when(isset($this->changed_by), $this->changed_by),
            'created_at' => $this->created_at,
        ];
    }
}
