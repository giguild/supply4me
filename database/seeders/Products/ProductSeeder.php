<?php

namespace Database\Seeders\Products;

use App\Models\Companies\Company;
use App\Models\Products\Product;
use App\Models\Products\ProductBrand;
use App\Models\Products\ProductCategory;
use App\Models\Products\ProductUnit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // Beverages
            ['name' => 'Coca-Cola 50cl', 'category' => 'Beverages', 'brand' => 'Coca-Cola', 'unit' => 'Piece', 'cost' => 150, 'sell' => 250, 'min_order' => 6, 'reorder' => 50, 'description' => 'Refreshing Coca-Cola in a 50cl bottle'],
            ['name' => 'Fanta Orange 50cl', 'category' => 'Beverages', 'brand' => 'Coca-Cola', 'unit' => 'Piece', 'cost' => 150, 'sell' => 250, 'min_order' => 6, 'reorder' => 50, 'description' => 'Fanta Orange flavoured drink 50cl'],
            ['name' => 'Pepsi 50cl', 'category' => 'Beverages', 'brand' => 'PepsiCo', 'unit' => 'Piece', 'cost' => 150, 'sell' => 250, 'min_order' => 6, 'reorder' => 50, 'description' => 'Pepsi cola 50cl bottle'],
            ['name' => 'Sprite 50cl', 'category' => 'Beverages', 'brand' => 'Coca-Cola', 'unit' => 'Piece', 'cost' => 150, 'sell' => 250, 'min_order' => 6, 'reorder' => 50, 'description' => 'Sprite lemon-lime drink 50cl'],
            ['name' => 'Nestle Pure Water 75cl', 'category' => 'Beverages', 'brand' => 'Nestle', 'unit' => 'Piece', 'cost' => 50, 'sell' => 100, 'min_order' => 12, 'reorder' => 100, 'description' => 'Nestle pure drinking water 75cl'],

            // Snacks
            ['name' => 'Indomie Instant Noodles 70g', 'category' => 'Snacks', 'brand' => 'Nestle', 'unit' => 'Piece', 'cost' => 80, 'sell' => 150, 'min_order' => 10, 'reorder' => 100, 'description' => 'Indomie instant noodles chicken flavour 70g'],
            ['name' => 'Chivita Fruit Juice 1L', 'category' => 'Snacks', 'brand' => 'Nestle', 'unit' => 'Piece', 'cost' => 350, 'sell' => 600, 'min_order' => 6, 'reorder' => 30, 'description' => 'Chivita fruit juice apple flavour 1 litre'],
            ['name' => 'Lay\'s Classic Salted Chips 95g', 'category' => 'Snacks', 'brand' => 'PepsiCo', 'unit' => 'Piece', 'cost' => 200, 'sell' => 350, 'min_order' => 6, 'reorder' => 40, 'description' => 'Lay\'s classic salted potato chips 95g'],
            ['name' => 'Milo Nuggets 70g', 'category' => 'Snacks', 'brand' => 'Nestle', 'unit' => 'Piece', 'cost' => 120, 'sell' => 200, 'min_order' => 10, 'reorder' => 60, 'description' => 'Milo chocolate nuggets 70g snack pack'],

            // Dairy
            ['name' => 'Peak Milk Powder 900g', 'category' => 'Dairy', 'brand' => 'Nestle', 'unit' => 'Piece', 'cost' => 2800, 'sell' => 4500, 'min_order' => 1, 'reorder' => 10, 'description' => 'Peak full cream milk powder 900g tin'],
            ['name' => 'Hollandia Yoghurt 1L', 'category' => 'Dairy', 'brand' => 'Nestle', 'unit' => 'Piece', 'cost' => 400, 'sell' => 700, 'min_order' => 6, 'reorder' => 20, 'description' => 'Hollandia strawberry yoghurt 1 litre'],
            ['name' => 'Three Crow Milk 400g', 'category' => 'Dairy', 'brand' => 'Nestle', 'unit' => 'Piece', 'cost' => 900, 'sell' => 1500, 'min_order' => 1, 'reorder' => 15, 'description' => 'Three Crow evaporated milk 400g'],

            // Cleaning
            ['name' => 'Omo Detergent 800g', 'category' => 'Cleaning', 'brand' => 'Unilever', 'unit' => 'Piece', 'cost' => 350, 'sell' => 600, 'min_order' => 6, 'reorder' => 30, 'description' => 'Omo washing powder 800g pack'],
            ['name' => 'Ariel Detergent 800g', 'category' => 'Cleaning', 'brand' => 'Procter & Gamble', 'unit' => 'Piece', 'cost' => 400, 'sell' => 700, 'min_order' => 6, 'reorder' => 25, 'description' => 'Ariel washing powder 800g'],
            ['name' => 'Viva Paper Towel 2 Rolls', 'category' => 'Cleaning', 'brand' => 'Procter & Gamble', 'unit' => 'Pack', 'cost' => 300, 'sell' => 500, 'min_order' => 4, 'reorder' => 20, 'description' => 'Viva paper towel 2-roll pack'],

            // Personal Care
            ['name' => 'Lux Soap 175g', 'category' => 'Personal Care', 'brand' => 'Unilever', 'unit' => 'Piece', 'cost' => 150, 'sell' => 250, 'min_order' => 12, 'reorder' => 50, 'description' => 'Lux beauty soap 175g bar'],
            ['name' => 'Colgate Toothpaste 100ml', 'category' => 'Personal Care', 'brand' => 'Procter & Gamble', 'unit' => 'Piece', 'cost' => 250, 'sell' => 450, 'min_order' => 6, 'reorder' => 30, 'description' => 'Colgate cavity protection toothpaste 100ml'],
            ['name' => 'Dettol Antiseptic 250ml', 'category' => 'Personal Care', 'brand' => 'Procter & Gamble', 'unit' => 'Piece', 'cost' => 400, 'sell' => 700, 'min_order' => 4, 'reorder' => 20, 'description' => 'Dettol antiseptic liquid 250ml'],

            // Frozen Foods
            ['name' => 'Tigo Frozen Chicken 1kg', 'category' => 'Frozen Foods', 'brand' => 'Nestle', 'unit' => 'Kilogram', 'cost' => 1800, 'sell' => 3000, 'min_order' => 1, 'reorder' => 10, 'description' => 'Tigo frozen whole chicken 1kg'],
            ['name' => 'Tigo Frozen Fish 500g', 'category' => 'Frozen Foods', 'brand' => 'Nestle', 'unit' => 'Kilogram', 'cost' => 800, 'sell' => 1400, 'min_order' => 1, 'reorder' => 15, 'description' => 'Tigo frozen catfish fillet 500g'],
        ];

        $company = Company::first();
        if (!$company) return;

        $categories = ProductCategory::where('company_id', $company->id)->get()->keyBy('name');
        $brands = ProductBrand::where('company_id', $company->id)->get()->keyBy('name');
        $units = ProductUnit::where('company_id', $company->id)->get()->keyBy('name');

        foreach ($products as $index => $item) {
            $category = $categories[$item['category']] ?? null;
            $brand = $brands[$item['brand']] ?? null;
            $unit = $units[$item['unit']] ?? null;

            if (!$category || !$brand || !$unit) continue;

            $sku = 'PRD-' . str_pad($index + 1, 5, '0', STR_PAD_LEFT);

            Product::firstOrCreate(
                ['company_id' => $company->id, 'sku' => $sku],
                [
                    'id' => Str::uuid(),
                    'name' => $item['name'],
                    'slug' => Str::slug($item['name']),
                    'description' => $item['description'],
                    'category_id' => $category->id,
                    'brand_id' => $brand->id,
                    'unit_id' => $unit->id,
                    'cost_price' => $item['cost'],
                    'selling_price' => $item['sell'],
                    'minimum_price' => round($item['cost'] * 0.8, 2),
                    'tax_rate' => 7.5,
                    'minimum_order_quantity' => $item['min_order'],
                    'reorder_level' => $item['reorder'],
                    'reorder_quantity' => $item['reorder'] * 2,
                    'status' => 'active',
                    'is_sellable' => true,
                    'is_purchasable' => true,
                    'is_stockable' => true,
                    'is_featured' => $index < 4,
                ]
            );
        }
    }
}
