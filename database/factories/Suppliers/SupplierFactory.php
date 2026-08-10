<?php

namespace Database\Factories\Suppliers;

use App\Models\Companies\Company;
use App\Models\Suppliers\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'company_id' => Company::factory(),
            'supplier_number' => 'SUP-' . strtoupper(Str::random(6)),
            'name' => fake()->company(),
            'contact_person' => fake()->name(),
            'tax_number' => fake()->optional(0.8)->bothify('TAX-####-####'),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'mobile' => fake()->phoneNumber(),
            'website' => fake()->optional(0.5)->url(),
            'address_line_1' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->stateAbbr(),
            'postal_code' => fake()->postcode(),
            'country' => 'NG',
            'payment_terms_days' => fake()->randomElement([0, 15, 30, 45, 60]),
            'lead_time_days' => fake()->numberBetween(1, 14),
            'minimum_order_amount' => fake()->randomFloat(2, 50, 500),
            'rating' => fake()->randomFloat(2, 1, 5),
            'bank_name' => fake()->optional(0.6)->company(),
            'bank_account_name' => fake()->optional(0.6)->name(),
            'bank_account_number' => fake()->optional(0.6)->bothify('########'),
            'status' => 'active',
            'notes' => fake()->optional(0.3)->sentence(),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }
}
