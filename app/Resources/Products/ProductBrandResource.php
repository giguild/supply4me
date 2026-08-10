<?php

namespace App\Resources\Products;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductBrandResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->when(isset($this->description), $this->description),
            'website' => $this->when(isset($this->website), $this->website),
            'logo' => $this->when(isset($this->logo), $this->logo),
            'is_active' => $this->when(isset($this->is_active), $this->is_active),
            'products_count' => $this->whenCounted('products'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
