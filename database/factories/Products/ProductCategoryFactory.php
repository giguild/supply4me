<?php

namespace Database\Factories\Products;

use App\Models\Companies\Company;
use App\Models\Products\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductCategoryFactory extends Factory
{
    protected $model = ProductCategory::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'id' => Str::uuid(),
            'company_id' => Company::factory(),
            'name' => ucfirst($name),
            'slug' => Str::slug($name) . '-' . Str::random(5),
            'description' => fake()->optional(0.6)->sentence(),
            'sort_order' => fake()->numberBetween(1, 100),
            'status' => 'active',
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }
}
