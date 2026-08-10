<?php

namespace App\Resources\Products;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductUnitResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'short_name' => $this->when(isset($this->short_name), $this->short_name),
            'type' => $this->when(isset($this->type), $this->type),
            'base_unit_id' => $this->when(isset($this->base_unit_id), $this->base_unit_id),
            'conversion_factor' => $this->when(isset($this->conversion_factor), $this->conversion_factor),
        ];
    }
}
