<?php

namespace App\Actions\Products;

use App\Enums\Products\ProductStatus;
use App\Enums\Products\ProductType;
use App\Models\Products\Product;

class UpdateProductAction
{
    public function execute(Product $product, array $data): Product
    {
        if (isset($data['product_type'])) {
            $data['product_type'] = ProductType::from($data['product_type']);
        }

        if (isset($data['status'])) {
            $data['status'] = ProductStatus::from($data['status']);
        }

        $product->update($data);

        return $product->fresh();
    }
}
