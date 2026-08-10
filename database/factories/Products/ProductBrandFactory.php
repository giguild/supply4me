<?php

namespace Database\Factories\Products;

use App\Models\Companies\Company;
use App\Models\Products\ProductBrand;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductBrandFactory extends Factory
{
    protected $model = ProductBrand::class;

    public function definition(): array
    {
        $name = fake()->unique()->company();

        return [
            'id' => Str::uuid(),
            'company_id' => Company::factory(),
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::random(5),
            'logo' => fake()->optional(0.3)->imageUrl(200, 200, 'brand', true),
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
