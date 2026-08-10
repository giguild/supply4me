<?php

namespace Database\Factories\Customers;

use App\Models\Companies\Company;
use App\Models\Customers\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'company_id' => Company::factory(),
            'customer_number' => 'CUST-' . strtoupper(Str::random(6)),
            'name' => fake()->company(),
            'trade_name' => fake()->optional(0.7)->company(),
            'customer_type' => fake()->randomElement(['individual', 'business', 'government', 'ngo']),
            'tax_number' => fake()->optional(0.8)->bothify('TAX-####-####'),
            'registration_number' => fake()->optional(0.6)->bothify('REG-####-####'),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'mobile' => fake()->phoneNumber(),
            'website' => fake()->optional(0.5)->url(),
            'address_line_1' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->stateAbbr(),
            'postal_code' => fake()->postcode(),
            'country' => 'NG',
            'latitude' => fake()->latitude(25, 48),
            'longitude' => fake()->longitude(-125, -70),
            'credit_limit' => fake()->randomFloat(2, 1000, 50000),
            'payment_terms_days' => fake()->randomElement([0, 15, 30, 45, 60]),
            'discount_percentage' => fake()->randomFloat(2, 0, 15),
            'status' => 'active',
            'credit_status' => 'good',
            'notes' => fake()->optional(0.3)->sentence(),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    public function suspended(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'suspended',
            'credit_status' => 'suspended',
        ]);
    }

    public function business(): static
    {
        return $this->state(fn (array $attributes) => [
            'customer_type' => 'business',
        ]);
    }
}
