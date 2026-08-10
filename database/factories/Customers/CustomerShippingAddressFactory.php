<?php

namespace Database\Factories\Customers;

use App\Models\Customers\Customer;
use App\Models\Customers\CustomerShippingAddress;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CustomerShippingAddressFactory extends Factory
{
    protected $model = CustomerShippingAddress::class;

    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'customer_id' => Customer::factory(),
            'label' => fake()->randomElement(['Main Office', 'Warehouse', 'Branch 1', 'Branch 2', 'Distribution Center']),
            'address_line_1' => fake()->streetAddress(),
            'address_line_2' => fake()->optional(0.3)->secondaryAddress(),
            'city' => fake()->city(),
            'state' => fake()->stateAbbr(),
            'postal_code' => fake()->postcode(),
            'country' => 'NG',
            'latitude' => fake()->latitude(25, 48),
            'longitude' => fake()->longitude(-125, -70),
            'delivery_instructions' => fake()->optional(0.4)->sentence(),
            'is_default' => false,
            'status' => 'active',
        ];
    }

    public function default(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_default' => true,
            'label' => 'Main Address',
        ]);
    }
}
