<?php

namespace Database\Factories\Payments;

use App\Models\Companies\Company;
use App\Models\Customers\Customer;
use App\Models\Payments\Payment;
use App\Models\Suppliers\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        $paymentType = fake()->randomElement(['incoming', 'outgoing']);

        return [
            'id' => Str::uuid(),
            'company_id' => Company::factory(),
            'payment_number' => 'PAY-' . strtoupper(Str::random(8)),
            'customer_id' => $paymentType === 'incoming' ? Customer::factory() : null,
            'supplier_id' => $paymentType === 'outgoing' ? Supplier::factory() : null,
            'payment_type' => $paymentType,
            'payment_method' => fake()->randomElement(['cash', 'bank_transfer', 'credit_card', 'check', 'mobile_money']),
            'reference_number' => fake()->optional(0.7)->bothify('REF-####-####'),
            'bank_name' => fake()->optional(0.5)->company(),
            'amount' => fake()->randomFloat(2, 100, 50000),
            'currency_code' => 'NGN',
            'exchange_rate' => 1,
            'payment_date' => fake()->dateTimeBetween('-30 days', 'now'),
            'status' => fake()->randomElement(['pending', 'approved', 'completed']),
            'version' => 1,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'approved_at' => now(),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'approved_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'rejection_reason' => fake()->sentence(),
        ]);
    }

    public function incoming(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_type' => 'incoming',
            'customer_id' => Customer::factory(),
            'supplier_id' => null,
        ]);
    }

    public function outgoing(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_type' => 'outgoing',
            'customer_id' => null,
            'supplier_id' => Supplier::factory(),
        ]);
    }
}
