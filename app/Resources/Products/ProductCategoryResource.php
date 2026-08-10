<?php

namespace App\Resources\Products;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->when(isset($this->description), $this->description),
            'parent_id' => $this->when(isset($this->parent_id), $this->parent_id),
            'sort_order' => $this->when(isset($this->sort_order), $this->sort_order),
            'is_active' => $this->when(isset($this->is_active), $this->is_active),
            'products_count' => $this->whenCounted('products'),
            'parent' => new self($this->whenLoaded('parent')),
            'children' => self::collection($this->whenLoaded('children')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
