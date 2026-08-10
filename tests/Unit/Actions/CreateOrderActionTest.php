<?php

use App\Actions\Orders\CreateOrderAction;
use App\Models\Core\User;
use App\Models\Customers\Customer;
use App\Models\Inventory\StockItem;
use App\Models\Inventory\Warehouse;
use App\Models\Products\Product;

it('creates an order with correct data', function () {
    $user = User::factory()->create();
    $customer = Customer::factory()->create(['company_id' => $user->company_id]);
    $product = Product::factory()->create(['company_id' => $user->company_id, 'selling_price' => 50]);
    $warehouse = Warehouse::factory()->create(['company_id' => $user->company_id]);

    StockItem::factory()->create([
        'company_id' => $user->company_id,
        'product_id' => $product->id,
        'warehouse_id' => $warehouse->id,
        'quantity_on_hand' => 100,
    ]);

    $action = app(CreateOrderAction::class);

    $order = $action->execute([
        'company_id' => $user->company_id,
        'customer_id' => $customer->id,
        'warehouse_id' => $warehouse->id,
        'items' => [
            [
                'product_id' => $product->id,
                'name' => $product->name,
                'quantity' => 5,
                'unit_price' => 50,
                'tax_rate' => 10,
            ],
        ],
    ], $user);

    expect($order)->toBeInstanceOf(\App\Models\Orders\Order::class)
        ->and($order->customer_id)->toBe($customer->id)
        ->and($order->status->value)->toBe('pending')
        ->and($order->items)->toHaveCount(1)
        ->and($order->subtotal)->toBe('250.00')
        ->and($order->tax_amount)->toBe('25.00')
        ->and($order->total_amount)->toBe('275.00');
});

it('creates an order with multiple items', function () {
    $user = User::factory()->create();
    $customer = Customer::factory()->create(['company_id' => $user->company_id]);
    $product1 = Product::factory()->create(['company_id' => $user->company_id, 'selling_price' => 100]);
    $product2 = Product::factory()->create(['company_id' => $user->company_id, 'selling_price' => 200]);

    $action = app(CreateOrderAction::class);

    $order = $action->execute([
        'company_id' => $user->company_id,
        'customer_id' => $customer->id,
        'items' => [
            [
                'product_id' => $product1->id,
                'name' => 'Product 1',
                'quantity' => 2,
                'unit_price' => 100,
                'tax_rate' => 0,
            ],
            [
                'product_id' => $product2->id,
                'name' => 'Product 2',
                'quantity' => 1,
                'unit_price' => 200,
                'tax_rate' => 0,
            ],
        ],
    ], $user);

    expect($order->items)->toHaveCount(2)
        ->and($order->subtotal)->toBe('400.00')
        ->and($order->total_amount)->toBe('400.00');
});

it('applies discount to order', function () {
    $user = User::factory()->create();
    $customer = Customer::factory()->create(['company_id' => $user->company_id]);
    $product = Product::factory()->create(['company_id' => $user->company_id, 'selling_price' => 100]);

    $action = app(CreateOrderAction::class);

    $order = $action->execute([
        'company_id' => $user->company_id,
        'customer_id' => $customer->id,
        'discount_amount' => 50,
        'items' => [
            [
                'product_id' => $product->id,
                'name' => 'Product',
                'quantity' => 1,
                'unit_price' => 100,
            ],
        ],
    ], $user);

    expect($order->discount_amount)->toBe('50.00')
        ->and($order->total_amount)->toBe('50.00');
});

it('applies shipping cost to order', function () {
    $user = User::factory()->create();
    $customer = Customer::factory()->create(['company_id' => $user->company_id]);
    $product = Product::factory()->create(['company_id' => $user->company_id, 'selling_price' => 100]);

    $action = app(CreateOrderAction::class);

    $order = $action->execute([
        'company_id' => $user->company_id,
        'customer_id' => $customer->id,
        'shipping_amount' => 25,
        'items' => [
            [
                'product_id' => $product->id,
                'name' => 'Product',
                'quantity' => 1,
                'unit_price' => 100,
            ],
        ],
    ], $user);

    expect($order->shipping_amount)->toBe('25.00')
        ->and($order->total_amount)->toBe('125.00');
});

it('creates order items with correct data', function () {
    $user = User::factory()->create();
    $customer = Customer::factory()->create(['company_id' => $user->company_id]);
    $product = Product::factory()->create(['company_id' => $user->company_id, 'selling_price' => 75]);

    $action = app(CreateOrderAction::class);

    $order = $action->execute([
        'company_id' => $user->company_id,
        'customer_id' => $customer->id,
        'items' => [
            [
                'product_id' => $product->id,
                'name' => $product->name,
                'quantity' => 3,
                'unit_price' => 75,
                'discount_percentage' => 10,
                'tax_rate' => 5,
                'notes' => 'Gift wrap',
            ],
        ],
    ], $user);

    $item = $order->items->first();

    expect($item->product_id)->toBe($product->id)
        ->and($item->quantity)->toBe(3)
        ->and($item->unit_price)->toBe('75.00')
        ->and($item->discount_percentage)->toBe('10.00')
        ->and($item->notes)->toBe('Gift wrap');
});
