<?php

namespace Database\Factories\Inventory;

use App\Models\Companies\Company;
use App\Models\Inventory\StockItem;
use App\Models\Inventory\Warehouse;
use App\Models\Products\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class StockItemFactory extends Factory
{
    protected $model = StockItem::class;

    public function definition(): array
    {
        $quantityOnHand = fake()->numberBetween(0, 1000);
        $quantityReserved = fake()->numberBetween(0, $quantityOnHand);

        return [
            'id' => Str::uuid(),
            'company_id' => Company::factory(),
            'warehouse_id' => Warehouse::factory(),
            'product_id' => Product::factory(),
            'quantity_on_hand' => $quantityOnHand,
            'quantity_reserved' => $quantityReserved,
            'quantity_on_order' => fake()->numberBetween(0, 200),
            'reorder_level' => fake()->numberBetween(5, 50),
            'reorder_quantity' => fake()->numberBetween(10, 200),
            'cost_price' => fake()->randomFloat(2, 5, 100),
            'status' => 'active',
            'version' => 1,
        ];
    }

    public function lowStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'quantity_on_hand' => 5,
            'reorder_level' => 10,
        ]);
    }

    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'quantity_on_hand' => 0,
        ]);
    }

    public function quarantine(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'quarantine',
        ]);
    }
}
