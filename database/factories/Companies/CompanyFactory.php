<?php

namespace Database\Factories\Companies;

use App\Models\Companies\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        $name = fake()->company();

        return [
            'id' => Str::uuid(),
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::random(5),
            'registration_number' => 'REG-' . strtoupper(Str::random(8)),
            'tax_number' => 'TAX-' . strtoupper(Str::random(8)),
            'email' => fake()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'address_line_1' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->stateAbbr(),
            'postal_code' => fake()->postcode(),
            'country' => 'NG',
            'currency_code' => 'NGN',
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
