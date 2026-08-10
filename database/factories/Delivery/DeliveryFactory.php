<?php

namespace Database\Factories\Delivery;

use App\Models\Companies\Company;
use App\Models\Customers\Customer;
use App\Models\Delivery\Delivery;
use App\Models\Delivery\Driver;
use App\Models\Orders\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DeliveryFactory extends Factory
{
    protected $model = Delivery::class;

    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'company_id' => Company::factory(),
            'delivery_number' => 'DEL-' . strtoupper(Str::random(8)),
            'order_id' => Order::factory(),
            'driver_id' => Driver::factory(),
            'customer_id' => Customer::factory(),
            'delivery_address' => fake()->streetAddress() . ', ' . fake()->city() . ', ' . fake()->stateAbbr() . ' ' . fake()->postcode(),
            'delivery_latitude' => fake()->latitude(25, 48),
            'delivery_longitude' => fake()->longitude(-125, -70),
            'scheduled_date' => fake()->dateTimeBetween('now', '+14 days'),
            'scheduled_time_start' => fake()->time('H:i:s'),
            'scheduled_time_end' => fake()->time('H:i:s'),
            'status' => fake()->randomElement(['pending', 'assigned', 'in_transit', 'delivered']),
            'attempt_count' => 0,
            'max_attempts' => 3,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    public function assigned(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'assigned',
        ]);
    }

    public function inTransit(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'in_transit',
        ]);
    }

    public function delivered(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'delivered',
            'actual_delivery_date' => now()->toDateString(),
            'actual_delivery_time' => now()->format('H:i:s'),
            'delivery_notes' => 'Delivered successfully',
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
            'attempt_count' => 1,
            'failure_reason' => fake()->sentence(),
        ]);
    }

    public function rescheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rescheduled',
            'rescheduled_date' => fake()->dateTimeBetween('+1 days', '+7 days'),
        ]);
    }
}
