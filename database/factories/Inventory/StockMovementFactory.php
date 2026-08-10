<?php

namespace Database\Factories\Inventory;

use App\Models\Companies\Company;
use App\Models\Inventory\StockItem;
use App\Models\Inventory\StockMovement;
use App\Models\Core\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class StockMovementFactory extends Factory
{
    protected $model = StockMovement::class;

    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 100);
        $quantityBefore = fake()->numberBetween($quantity, $quantity + 500);

        return [
            'id' => Str::uuid(),
            'company_id' => Company::factory(),
            'stock_item_id' => StockItem::factory(),
            'movement_type' => fake()->randomElement(['purchase', 'sale', 'transfer', 'adjustment', 'return']),
            'quantity' => $quantity,
            'quantity_before' => $quantityBefore,
            'quantity_after' => $quantityBefore + $quantity,
            'unit_cost' => fake()->randomFloat(2, 5, 100),
            'total_cost' => fake()->randomFloat(2, 50, 10000),
            'reason' => fake()->optional(0.5)->sentence(),
            'performed_by' => User::factory(),
            'status' => 'approved',
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    public function purchase(): static
    {
        return $this->state(fn (array $attributes) => [
            'movement_type' => 'purchase',
        ]);
    }

    public function sale(): static
    {
        return $this->state(fn (array $attributes) => [
            'movement_type' => 'sale',
            'quantity' => -fake()->numberBetween(1, 50),
        ]);
    }

    public function adjustment(): static
    {
        return $this->state(fn (array $attributes) => [
            'movement_type' => 'adjustment',
        ]);
    }

    public function transfer(): static
    {
        return $this->state(fn (array $attributes) => [
            'movement_type' => 'transfer',
        ]);
    }
}
