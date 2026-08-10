<?php

namespace App\Actions\Products;

use App\Models\Products\ProductCategory;
use Illuminate\Support\Str;

class CreateProductCategoryAction
{
    public function execute(array $data): ProductCategory
    {
        return ProductCategory::create([
            'company_id' => $data['company_id'],
            'parent_id' => $data['parent_id'] ?? null,
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'description' => $data['description'] ?? null,
            'image' => $data['image'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'status' => $data['status'] ?? 'active',
        ]);
    }
}
