<?php

use App\Models\Core\User;
use App\Models\Customers\Customer;
use App\Models\Orders\Order;
use App\Models\Orders\OrderItem;
use App\Models\Products\Product;
use App\Services\Order\OrderCalculationService;
use App\ValueObjects\Money;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->customer = Customer::factory()->create(['company_id' => $this->user->company_id]);
    $this->product = Product::factory()->create(['company_id' => $this->user->company_id, 'selling_price' => 100]);
    $this->service = app(OrderCalculationService::class);
});

it('calculates subtotal from order items', function () {
    $order = Order::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $this->product->id,
        'quantity' => 10,
        'unit_price' => 100,
    ]);

    $subtotal = $this->service->calculateSubtotal($order);

    expect($subtotal)->toBeInstanceOf(Money::class)
        ->and($subtotal->getAmount())->toBe(1000.00);
});

it('calculates shipping from order', function () {
    $order = Order::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'shipping_amount' => 50,
    ]);

    $shipping = $this->service->calculateShipping($order);

    expect($shipping)->toBeInstanceOf(Money::class)
        ->and($shipping->getAmount())->toBe(50.00);
});

it('calculates total with all components', function () {
    $order = Order::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'subtotal' => 1000,
        'tax_amount' => 100,
        'discount_amount' => 50,
        'shipping_amount' => 25,
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $this->product->id,
        'quantity' => 10,
        'unit_price' => 100,
    ]);

    $total = $this->service->calculateTotal($order);

    expect($total)->toBeInstanceOf(Money::class)
        ->and($total->getAmount())->toBe(1075.00);
});

it('recalculates and updates order totals', function () {
    $order = Order::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $this->product->id,
        'quantity' => 5,
        'unit_price' => 100,
    ]);

    $this->service->recalculateOrder($order);

    $order->refresh();

    expect($order->subtotal)->toBe('500.00')
        ->and($order->total_amount)->not->toBeNull();
});

it('handles order with no items', function () {
    $order = Order::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
    ]);

    $subtotal = $this->service->calculateSubtotal($order);

    expect($subtotal->getAmount())->toBe(0.00);
});
