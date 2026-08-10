<?php

namespace Database\Factories\Invoicing;

use App\Models\Companies\Company;
use App\Models\Customers\Customer;
use App\Models\Invoicing\Invoice;
use App\Models\Orders\Order;
use App\Models\Core\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 100, 10000);
        $discountAmount = fake()->randomFloat(2, 0, $subtotal * 0.2);
        $taxRate = fake()->randomFloat(2, 0, 0.2);
        $taxAmount = ($subtotal - $discountAmount) * $taxRate;
        $shippingAmount = fake()->randomFloat(2, 0, 500);
        $totalAmount = $subtotal - $discountAmount + $taxAmount + $shippingAmount;
        $paidAmount = fake()->randomFloat(2, 0, $totalAmount);

        return [
            'id' => Str::uuid(),
            'company_id' => Company::factory(),
            'invoice_number' => 'INV-' . strtoupper(Str::random(8)),
            'customer_id' => Customer::factory(),
            'order_id' => Order::factory(),
            'invoice_type' => 'sales',
            'status' => fake()->randomElement(['draft', 'sent', 'paid', 'partial', 'overdue']),
            'invoice_date' => fake()->dateTimeBetween('-30 days', 'now'),
            'due_date' => fake()->dateTimeBetween('+1 days', '+60 days'),
            'currency_code' => 'NGN',
            'exchange_rate' => 1,
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'tax_amount' => round($taxAmount, 2),
            'shipping_amount' => $shippingAmount,
            'total_amount' => round($totalAmount, 2),
            'paid_amount' => round($paidAmount, 2),
            'due_amount' => round($totalAmount - $paidAmount, 2),
            'payment_terms_days' => fake()->randomElement([0, 15, 30]),
            'created_by' => User::factory(),
            'version' => 1,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
            'paid_amount' => $attributes['total_amount'],
            'due_amount' => 0,
        ]);
    }

    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'overdue',
            'due_date' => fake()->dateTimeBetween('-30 days', '-1 day'),
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
