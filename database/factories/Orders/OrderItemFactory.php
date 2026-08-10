<?php

namespace Database\Factories\Orders;

use App\Models\Orders\Order;
use App\Models\Orders\OrderItem;
use App\Models\Products\Product;
use App\Models\Products\ProductUnit;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 100);
        $unitPrice = fake()->randomFloat(2, 5, 200);
        $discountPercentage = fake()->randomFloat(2, 0, 15);
        $discountAmount = ($quantity * $unitPrice) * ($discountPercentage / 100);
        $taxRate = fake()->randomFloat(2, 0, 20);
        $taxAmount = ($quantity * $unitPrice - $discountAmount) * ($taxRate / 100);
        $lineTotal = $quantity * $unitPrice - $discountAmount + $taxAmount;

        return [
            'id' => Str::uuid(),
            'order_id' => Order::factory(),
            'product_id' => Product::factory(),
            'sku' => 'SKU-' . strtoupper(Str::random(8)),
            'name' => fake()->words(3, true),
            'unit_id' => ProductUnit::factory(),
            'quantity' => $quantity,
            'quantity_shipped' => 0,
            'quantity_delivered' => 0,
            'quantity_returned' => 0,
            'unit_price' => $unitPrice,
            'discount_percentage' => $discountPercentage,
            'discount_amount' => round($discountAmount, 2),
            'tax_rate' => $taxRate,
            'tax_amount' => round($taxAmount, 2),
            'line_total' => round($lineTotal, 2),
            'sort_order' => fake()->numberBetween(1, 20),
        ];
    }
}
