<?php

namespace Database\Seeders\Products;

use App\Models\Companies\Company;
use App\Models\Products\ProductCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Beverages',
            'Snacks',
            'Dairy',
            'Produce',
            'Cleaning',
            'Electronics',
            'Office Supplies',
            'Personal Care',
            'Frozen Foods',
            'Canned Goods',
            'Bakery',
            'Meat & Seafood',
            'Confectionery',
            'Health & Wellness',
            'Household Items',
        ];

        $companies = Company::all();

        foreach ($companies as $company) {
            foreach ($categories as $index => $categoryName) {
                ProductCategory::firstOrCreate(
                    ['company_id' => $company->id, 'slug' => Str::slug($categoryName)],
                    [
                        'id' => \Illuminate\Support\Str::uuid(),
                        'name' => $categoryName,
                        'description' => "{$categoryName} products",
                        'sort_order' => $index + 1,
                        'status' => 'active',
                    ]
                );
            }
        }
    }
}
