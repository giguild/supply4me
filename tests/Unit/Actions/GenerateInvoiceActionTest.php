<?php

use App\Actions\Invoicing\GenerateInvoiceAction;
use App\Enums\Invoicing\InvoiceStatus;
use App\Models\Core\User;
use App\Models\Customers\Customer;
use App\Models\Orders\Order;
use App\Models\Orders\OrderItem;
use App\Models\Products\Product;

it('generates an invoice from an order', function () {
    $user = User::factory()->create();
    $customer = Customer::factory()->create(['company_id' => $user->company_id]);
    $product = Product::factory()->create(['company_id' => $user->company_id, 'selling_price' => 100]);

    $order = Order::factory()->create([
        'company_id' => $user->company_id,
        'customer_id' => $customer->id,
        'subtotal' => 500,
        'tax_amount' => 50,
        'total_amount' => 550,
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => 5,
        'unit_price' => 100,
    ]);

    $action = app(GenerateInvoiceAction::class);
    $invoice = $action->execute($order, $user);

    expect($invoice)->toBeInstanceOf(\App\Models\Invoicing\Invoice::class)
        ->and($invoice->status)->toBe(InvoiceStatus::Draft)
        ->and($invoice->order_id)->toBe($order->id)
        ->and($invoice->customer_id)->toBe($customer->id)
        ->and($invoice->subtotal)->toBe('500.00')
        ->and($invoice->tax_amount)->toBe('50.00')
        ->and($invoice->total_amount)->toBe('550.00')
        ->and($invoice->balance_due)->toBe('550.00')
        ->and($invoice->amount_paid)->toBe('0.00');
});

it('creates invoice items from order items', function () {
    $user = User::factory()->create();
    $customer = Customer::factory()->create(['company_id' => $user->company_id]);
    $product1 = Product::factory()->create(['company_id' => $user->company_id, 'selling_price' => 100]);
    $product2 = Product::factory()->create(['company_id' => $user->company_id, 'selling_price' => 200]);

    $order = Order::factory()->create([
        'company_id' => $user->company_id,
        'customer_id' => $customer->id,
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product1->id,
        'quantity' => 3,
        'unit_price' => 100,
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product2->id,
        'quantity' => 2,
        'unit_price' => 200,
    ]);

    $action = app(GenerateInvoiceAction::class);
    $invoice = $action->execute($order, $user);

    expect($invoice->items)->toHaveCount(2);

    $this->assertDatabaseHas('invoice_items', [
        'invoice_id' => $invoice->id,
        'product_id' => $product1->id,
        'quantity' => 3,
    ]);

    $this->assertDatabaseHas('invoice_items', [
        'invoice_id' => $invoice->id,
        'product_id' => $product2->id,
        'quantity' => 2,
    ]);
});

it('copies order totals to invoice', function () {
    $user = User::factory()->create();
    $customer = Customer::factory()->create(['company_id' => $user->company_id]);
    $product = Product::factory()->create(['company_id' => $user->company_id]);

    $order = Order::factory()->create([
        'company_id' => $user->company_id,
        'customer_id' => $customer->id,
        'subtotal' => 1000,
        'tax_amount' => 100,
        'discount_amount' => 50,
        'shipping_amount' => 25,
        'total_amount' => 1075,
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
    ]);

    $action = app(GenerateInvoiceAction::class);
    $invoice = $action->execute($order, $user);

    expect($invoice->subtotal)->toBe('1000.00')
        ->and($invoice->tax_amount)->toBe('100.00')
        ->and($invoice->discount_amount)->toBe('50.00')
        ->and($invoice->total_amount)->toBe('1075.00');
});

it('sets invoice due date from order', function () {
    $user = User::factory()->create();
    $customer = Customer::factory()->create(['company_id' => $user->company_id]);
    $product = Product::factory()->create(['company_id' => $user->company_id]);

    $dueDate = now()->addDays(30);

    $order = Order::factory()->create([
        'company_id' => $user->company_id,
        'customer_id' => $customer->id,
        'due_date' => $dueDate,
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
    ]);

    $action = app(GenerateInvoiceAction::class);
    $invoice = $action->execute($order, $user);

    expect($invoice->due_date->format('Y-m-d'))->toBe($dueDate->format('Y-m-d'));
});

it('records created_by user', function () {
    $user = User::factory()->create();
    $customer = Customer::factory()->create(['company_id' => $user->company_id]);
    $product = Product::factory()->create(['company_id' => $user->company_id]);

    $order = Order::factory()->create([
        'company_id' => $user->company_id,
        'customer_id' => $customer->id,
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
    ]);

    $action = app(GenerateInvoiceAction::class);
    $invoice = $action->execute($order, $user);

    expect($invoice->created_by)->toBe($user->id);
});
