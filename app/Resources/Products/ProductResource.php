<?php

namespace App\Resources\Products;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'sku' => $this->sku,
            'barcode' => $this->when(isset($this->barcode), $this->barcode),
            'description' => $this->when(isset($this->description), $this->description),
            'category_id' => $this->category_id,
            'brand_id' => $this->when(isset($this->brand_id), $this->brand_id),
            'unit_id' => $this->unit_id,
            'cost_price' => (float) $this->cost_price,
            'selling_price' => (float) $this->selling_price,
            'tax_rate' => $this->when(isset($this->tax_rate), (float) $this->tax_rate),
            'type' => $this->when(isset($this->type), $this->type),
            'status' => $this->when(isset($this->status), $this->status),
            'weight' => $this->when(isset($this->weight), $this->weight),
            'dimensions' => $this->when(isset($this->dimensions), $this->dimensions),
            'minimum_stock_level' => $this->when(isset($this->minimum_stock_level), $this->minimum_stock_level),
            'reorder_point' => $this->when(isset($this->reorder_point), $this->reorder_point),
            'is_taxable' => $this->when(isset($this->is_taxable), $this->is_taxable),
            'is_active' => $this->when(isset($this->is_active), $this->is_active),
            'category' => new ProductCategoryResource($this->whenLoaded('category')),
            'brand' => new ProductBrandResource($this->whenLoaded('brand')),
            'unit' => new ProductUnitResource($this->whenLoaded('unit')),
            'variants' => ProductVariantResource::collection($this->whenLoaded('variants')),
            'images' => ProductImageResource::collection($this->whenLoaded('images')),
            'stock_items' => \App\Resources\Inventory\StockItemResource::collection($this->whenLoaded('stockItems')),
            'total_stock' => $this->when(
                $this->relationLoaded('stockItems'),
                fn () => $this->stockItems->sum('quantity_on_hand')
            ),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
