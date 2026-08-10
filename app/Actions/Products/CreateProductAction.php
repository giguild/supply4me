<?php

namespace App\Actions\Products;

use App\Enums\Products\ProductStatus;
use App\Enums\Products\ProductType;
use App\Models\Products\Product;
use App\Models\Products\ProductVariant;
use Illuminate\Support\Facades\DB;

class CreateProductAction
{
    public function execute(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            $product = Product::create([
                'company_id' => $data['company_id'],
                'sku' => $data['sku'] ?? null,
                'barcode' => $data['barcode'] ?? null,
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'short_description' => $data['short_description'] ?? null,
                'category_id' => $data['category_id'] ?? null,
                'brand_id' => $data['brand_id'] ?? null,
                'unit_id' => $data['unit_id'] ?? null,
                'product_type' => ProductType::from($data['product_type'] ?? ProductType::Standard->value),
                'is_sellable' => $data['is_sellable'] ?? true,
                'is_purchasable' => $data['is_purchasable'] ?? true,
                'is_stockable' => $data['is_stockable'] ?? true,
                'weight' => $data['weight'] ?? null,
                'weight_unit' => $data['weight_unit'] ?? null,
                'dimensions' => $data['dimensions'] ?? null,
                'cost_price' => $data['cost_price'] ?? 0,
                'selling_price' => $data['selling_price'] ?? 0,
                'minimum_price' => $data['minimum_price'] ?? null,
                'tax_rate' => $data['tax_rate'] ?? 0,
                'reorder_level' => $data['reorder_level'] ?? 0,
                'reorder_quantity' => $data['reorder_quantity'] ?? 0,
                'minimum_order_quantity' => $data['minimum_order_quantity'] ?? 1,
                'shelf_life_days' => $data['shelf_life_days'] ?? null,
                'warranty_days' => $data['warranty_days'] ?? null,
                'status' => ProductStatus::from($data['status'] ?? ProductStatus::Active->value),
                'is_featured' => $data['is_featured'] ?? false,
                'tags' => $data['tags'] ?? null,
                'attributes' => $data['attributes'] ?? null,
                'metadata' => $data['metadata'] ?? [],
            ]);

            if (! empty($data['variants']) && is_array($data['variants'])) {
                foreach ($data['variants'] as $variantData) {
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'sku' => $variantData['sku'] ?? null,
                        'barcode' => $variantData['barcode'] ?? null,
                        'name' => $variantData['name'],
                        'cost_price' => $variantData['cost_price'] ?? $product->cost_price,
                        'selling_price' => $variantData['selling_price'] ?? $product->selling_price,
                        'stock_quantity' => $variantData['stock_quantity'] ?? 0,
                        'attributes' => $variantData['attributes'] ?? null,
                        'status' => $variantData['status'] ?? 'active',
                    ]);
                }
            }

            return $product;
        });
    }
}
