<?php

namespace Database\Factories\Orders;

use App\Models\Branches\Branch;
use App\Models\Companies\Company;
use App\Models\Customers\Customer;
use App\Models\Inventory\Warehouse;
use App\Models\Orders\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 100, 10000);
        $discountAmount = fake()->randomFloat(2, 0, $subtotal * 0.2);
        $taxRate = fake()->randomFloat(2, 0, 0.2);
        $taxAmount = ($subtotal - $discountAmount) * $taxRate;
        $shippingAmount = fake()->randomFloat(2, 0, 500);
        $totalAmount = $subtotal - $discountAmount + $taxAmount + $shippingAmount;

        return [
            'id' => Str::uuid(),
            'company_id' => Company::factory(),
            'order_number' => 'ORD-' . strtoupper(Str::random(8)),
            'customer_id' => Customer::factory(),
            'branch_id' => Branch::factory(),
            'warehouse_id' => Warehouse::factory(),
            'order_type' => 'sales',
            'status' => fake()->randomElement(['draft', 'pending', 'confirmed', 'processing']),
            'payment_status' => fake()->randomElement(['unpaid', 'partial', 'paid']),
            'fulfillment_status' => 'unfulfilled',
            'priority' => fake()->randomElement(['low', 'normal', 'high', 'urgent']),
            'order_date' => fake()->dateTimeBetween('-30 days', 'now'),
            'requested_delivery_date' => fake()->optional(0.7)->dateTimeBetween('now', '+30 days'),
            'currency_code' => 'NGN',
            'exchange_rate' => 1,
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'tax_amount' => round($taxAmount, 2),
            'shipping_amount' => $shippingAmount,
            'total_amount' => round($totalAmount, 2),
            'paid_amount' => 0,
            'due_amount' => round($totalAmount, 2),
            'payment_terms_days' => fake()->randomElement([0, 15, 30]),
            'version' => 1,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'payment_status' => 'paid',
            'fulfillment_status' => 'fulfilled',
            'completed_at' => now(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => fake()->sentence(),
        ]);
    }
}
