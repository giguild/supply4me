<?php

namespace App\Resources\Products;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'name' => $this->name,
            'sku' => $this->sku,
            'cost_price' => (float) $this->cost_price,
            'selling_price' => (float) $this->selling_price,
            'stock' => $this->when(isset($this->stock), $this->stock),
            'attributes' => $this->when(isset($this->attributes), $this->attributes),
            'is_active' => $this->when(isset($this->is_active), $this->is_active),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
