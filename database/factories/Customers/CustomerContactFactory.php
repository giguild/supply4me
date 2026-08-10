<?php

namespace Database\Factories\Customers;

use App\Models\Customers\Customer;
use App\Models\Customers\CustomerContact;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CustomerContactFactory extends Factory
{
    protected $model = CustomerContact::class;

    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'customer_id' => Customer::factory(),
            'name' => fake()->name(),
            'position' => fake()->jobTitle(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'mobile' => fake()->phoneNumber(),
            'is_primary' => false,
            'receives_invoices' => fake()->boolean(30),
            'receives_orders' => fake()->boolean(30),
            'status' => 'active',
        ];
    }

    public function primary(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_primary' => true,
            'receives_invoices' => true,
            'receives_orders' => true,
        ]);
    }
}
