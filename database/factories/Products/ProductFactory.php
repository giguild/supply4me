<?php

namespace Database\Factories\Products;

use App\Models\Companies\Company;
use App\Models\Products\Product;
use App\Models\Products\ProductBrand;
use App\Models\Products\ProductCategory;
use App\Models\Products\ProductUnit;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $company = Company::factory()->create();
        $unit = ProductUnit::where('company_id', $company->id)->first() ?? ProductUnit::factory()->create(['company_id' => $company->id]);

        return [
            'id' => Str::uuid(),
            'company_id' => $company->id,
            'sku' => 'SKU-' . strtoupper(Str::random(8)),
            'barcode' => fake()->optional(0.7)->bothify('############'),
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'short_description' => fake()->optional(0.5)->words(5, true),
            'category_id' => ProductCategory::factory(),
            'brand_id' => ProductBrand::factory(),
            'unit_id' => $unit->id,
            'product_type' => fake()->randomElement(['standard', 'service', 'bundle', 'digital']),
            'is_sellable' => true,
            'is_purchasable' => true,
            'is_stockable' => true,
            'weight' => fake()->optional(0.7)->randomFloat(3, 0.1, 50),
            'weight_unit' => 'kg',
            'cost_price' => fake()->randomFloat(2, 5, 100),
            'selling_price' => fake()->randomFloat(2, 10, 200),
            'minimum_price' => fake()->randomFloat(2, 5, 50),
            'tax_rate' => fake()->randomFloat(2, 0, 20),
            'reorder_level' => fake()->numberBetween(5, 50),
            'reorder_quantity' => fake()->numberBetween(10, 200),
            'minimum_order_quantity' => 1,
            'status' => 'active',
            'is_featured' => fake()->boolean(20),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }
}
