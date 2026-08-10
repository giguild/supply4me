<?php

namespace App\Resources\Products;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductImageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'path' => $this->path,
            'url' => $this->when(isset($this->path), fn () => asset('storage/' . $this->path)),
            'filename' => $this->when(isset($this->filename), $this->filename),
            'alt_text' => $this->when(isset($this->alt_text), $this->alt_text),
            'sort_order' => $this->when(isset($this->sort_order), $this->sort_order),
            'is_primary' => $this->when(isset($this->is_primary), $this->is_primary),
            'size' => $this->when(isset($this->size), $this->size),
            'mime_type' => $this->when(isset($this->mime_type), $this->mime_type),
            'created_at' => $this->created_at,
        ];
    }
}
