<?php

use App\Models\Core\User;
use App\Models\Customers\Customer;
use App\Models\Invoicing\Invoice;
use App\Models\Invoicing\InvoiceItem;
use App\Models\Orders\Order;
use App\Models\Orders\OrderItem;
use App\Models\Products\Product;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
    $this->customer = Customer::factory()->create(['company_id' => $this->user->company_id]);
    $this->product = Product::factory()->create(['company_id' => $this->user->company_id, 'selling_price' => 100]);
    $this->order = Order::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'subtotal' => 1000,
        'tax_amount' => 100,
        'total_amount' => 1100,
    ]);
    OrderItem::factory()->create([
        'order_id' => $this->order->id,
        'product_id' => $this->product->id,
        'quantity' => 10,
        'unit_price' => 100,
    ]);
});

it('can generate an invoice from an order', function () {
    $response = $this->postJson('/api/v1/invoices', [
        'order_id' => $this->order->id,
        'due_date' => now()->addDays(30)->toDateString(),
        'notes' => 'Payment due within 30 days',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'success',
            'message',
            'data',
        ]);

    $this->assertDatabaseHas('invoices', [
        'order_id' => $this->order->id,
        'customer_id' => $this->customer->id,
        'status' => 'draft',
    ]);
});

it('generates invoice with correct totals from order', function () {
    $this->postJson('/api/v1/invoices', [
        'order_id' => $this->order->id,
        'due_date' => now()->addDays(30)->toDateString(),
    ]);

    $invoice = Invoice::where('order_id', $this->order->id)->first();

    expect($invoice->subtotal)->toBe('1000.00')
        ->and($invoice->tax_amount)->toBe('100.00')
        ->and($invoice->total_amount)->toBe('1100.00')
        ->and($invoice->balance_due)->toBe('1100.00')
        ->and($invoice->amount_paid)->toBe('0.00');
});

it('creates invoice items from order items', function () {
    $this->postJson('/api/v1/invoices', [
        'order_id' => $this->order->id,
        'due_date' => now()->addDays(30)->toDateString(),
    ]);

    $invoice = Invoice::where('order_id', $this->order->id)->first();

    $this->assertDatabaseHas('invoice_items', [
        'invoice_id' => $invoice->id,
        'product_id' => $this->product->id,
        'quantity' => 10,
        'unit_price' => 100,
    ]);
});

it('validates required fields for invoice generation', function () {
    $response = $this->postJson('/api/v1/invoices', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['order_id', 'due_date']);
});

it('validates order exists for invoice', function () {
    $response = $this->postJson('/api/v1/invoices', [
        'order_id' => 99999,
        'due_date' => now()->addDays(30)->toDateString(),
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['order_id']);
});

it('validates due date is after today', function () {
    $response = $this->postJson('/api/v1/invoices', [
        'order_id' => $this->order->id,
        'due_date' => now()->subDays(5)->toDateString(),
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['due_date']);
});

it('can show an invoice', function () {
    $invoice = Invoice::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
    ]);

    $response = $this->getJson("/api/v1/invoices/{$invoice->id}");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data',
        ]);
});

it('can list invoices with filters', function () {
    Invoice::factory()->count(5)->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'pending',
    ]);

    $response = $this->getJson('/api/v1/invoices?status=pending');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data',
            'meta',
        ]);
});

it('can get invoice items', function () {
    $invoice = Invoice::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
    ]);

    InvoiceItem::factory()->count(3)->create(['invoice_id' => $invoice->id]);

    $response = $this->getJson("/api/v1/invoices/{$invoice->id}/items");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data',
        ]);
});
