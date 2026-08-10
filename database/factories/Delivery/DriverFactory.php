<?php

namespace Database\Factories\Delivery;

use App\Models\Companies\Company;
use App\Models\Core\User;
use App\Models\Delivery\Driver;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DriverFactory extends Factory
{
    protected $model = Driver::class;

    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'company_id' => Company::factory(),
            'user_id' => User::factory(),
            'name' => fake()->name(),
            'license_number' => strtoupper(Str::random(10)),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->safeEmail(),
            'vehicle_type' => fake()->randomElement(['van', 'truck', 'motorcycle', 'bicycle']),
            'vehicle_registration' => strtoupper(Str::random(8)),
            'status' => 'active',
            'current_latitude' => fake()->latitude(25, 48),
            'current_longitude' => fake()->longitude(-125, -70),
            'last_location_update' => now(),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    public function onLeave(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'on_leave',
        ]);
    }

    public function terminated(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'terminated',
        ]);
    }
}
