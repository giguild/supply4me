<?php

namespace Database\Factories\Branches;

use App\Models\Branches\Branch;
use App\Models\Companies\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BranchFactory extends Factory
{
    protected $model = Branch::class;

    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'company_id' => Company::factory(),
            'name' => fake()->city() . ' Branch',
            'code' => strtoupper(Str::random(3)),
            'type' => fake()->randomElement(['headquarters', 'warehouse', 'store', 'office', 'distribution_center']),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'address_line_1' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->stateAbbr(),
            'postal_code' => fake()->postcode(),
            'country' => 'NG',
            'latitude' => fake()->latitude(25, 48),
            'longitude' => fake()->longitude(-125, -70),
            'is_main' => false,
            'status' => 'active',
        ];
    }

    public function headquarters(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'headquarters',
            'is_main' => true,
        ]);
    }

    public function warehouse(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'warehouse',
        ]);
    }

    public function store(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'store',
        ]);
    }
}
