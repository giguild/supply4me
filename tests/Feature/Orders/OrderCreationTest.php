<?php

use App\Models\Core\User;
use App\Models\Customers\Customer;
use App\Models\Orders\Order;
use App\Models\Orders\OrderItem;
use App\Models\Products\Product;
use App\Models\Inventory\StockItem;
use App\Models\Inventory\Warehouse;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
    $this->customer = Customer::factory()->create(['company_id' => $this->user->company_id]);
    $this->warehouse = Warehouse::factory()->create(['company_id' => $this->user->company_id]);
    $this->product = Product::factory()->create(['company_id' => $this->user->company_id, 'selling_price' => 100]);
    StockItem::factory()->create([
        'company_id' => $this->user->company_id,
        'product_id' => $this->product->id,
        'warehouse_id' => $this->warehouse->id,
        'quantity_on_hand' => 100,
    ]);
});

it('can create an order with items', function () {
    $response = $this->postJson('/api/v1/orders', [
        'customer_id' => $this->customer->id,
        'warehouse_id' => $this->warehouse->id,
        'items' => [
            [
                'product_id' => $this->product->id,
                'name' => $this->product->name,
                'quantity' => 5,
                'unit_price' => 100,
                'tax_rate' => 10,
            ],
        ],
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => ['id', 'order_number', 'total_amount'],
        ]);

    $this->assertDatabaseHas('orders', [
        'customer_id' => $this->customer->id,
    ]);

    $this->assertDatabaseHas('order_items', [
        'product_id' => $this->product->id,
        'quantity' => 5,
        'unit_price' => 100,
    ]);
});

it('calculates correct order totals', function () {
    $response = $this->postJson('/api/v1/orders', [
        'customer_id' => $this->customer->id,
        'warehouse_id' => $this->warehouse->id,
        'items' => [
            [
                'product_id' => $this->product->id,
                'name' => 'Product 1',
                'quantity' => 10,
                'unit_price' => 100,
                'tax_rate' => 10,
                'discount_percentage' => 0,
            ],
        ],
        'shipping_amount' => 50,
        'discount_amount' => 100,
    ]);

    $response->assertStatus(201);

    $order = Order::where('customer_id', $this->customer->id)->first();

    expect($order->subtotal)->toBe('900.00')
        ->and($order->tax_amount)->toBe('90.00')
        ->and($order->discount_amount)->toBe('100.00')
        ->and($order->shipping_amount)->toBe('50.00')
        ->and($order->total_amount)->toBe('940.00');
});

it('validates required fields for order creation', function () {
    $response = $this->postJson('/api/v1/orders', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['customer_id', 'items']);
});

it('validates items array is not empty', function () {
    $response = $this->postJson('/api/v1/orders', [
        'customer_id' => $this->customer->id,
        'items' => [],
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['items']);
});

it('validates product exists for order items', function () {
    $response = $this->postJson('/api/v1/orders', [
        'customer_id' => $this->customer->id,
        'items' => [
            [
                'product_id' => 99999,
                'name' => 'Non-existent Product',
                'quantity' => 1,
                'unit_price' => 100,
            ],
        ],
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['items.0.product_id']);
});

it('validates quantity is at least 1', function () {
    $response = $this->postJson('/api/v1/orders', [
        'customer_id' => $this->customer->id,
        'items' => [
            [
                'product_id' => $this->product->id,
                'name' => 'Product',
                'quantity' => 0,
                'unit_price' => 100,
            ],
        ],
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['items.0.quantity']);
});

it('validates unit price is at least 0', function () {
    $response = $this->postJson('/api/v1/orders', [
        'customer_id' => $this->customer->id,
        'items' => [
            [
                'product_id' => $this->product->id,
                'name' => 'Product',
                'quantity' => 1,
                'unit_price' => -10,
            ],
        ],
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['items.0.unit_price']);
});

it('can show a specific order', function () {
    $order = Order::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
    ]);

    $response = $this->getJson("/api/v1/orders/{$order->id}");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data' => ['id', 'order_number', 'status'],
        ]);
});

it('can list orders with filters', function () {
    Order::factory()->count(5)->create([
        'company_id' => $this->user->company_id,
        'status' => 'pending',
    ]);

    $response = $this->getJson('/api/v1/orders?status=pending');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data',
            'meta',
        ]);
});

it('can update order notes and shipping address', function () {
    $order = Order::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'draft',
    ]);

    $response = $this->putJson("/api/v1/orders/{$order->id}", [
        'notes' => 'Updated notes',
        'shipping_address' => [
            'line1' => '123 New Street',
            'city' => 'New City',
            'state' => 'NC',
            'country' => 'US',
            'postal_code' => '12345',
        ],
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Order updated successfully',
        ]);
});

it('can delete a draft order', function () {
    $order = Order::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'draft',
    ]);

    $response = $this->deleteJson("/api/v1/orders/{$order->id}");

    $response->assertStatus(200);

    $this->assertSoftDeleted('orders', ['id' => $order->id]);
});

it('cannot delete a confirmed order', function () {
    $order = Order::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'confirmed',
    ]);

    $response = $this->deleteJson("/api/v1/orders/{$order->id}");

    $response->assertStatus(422);
});

it('can add items to an order', function () {
    $order = Order::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'draft',
    ]);

    $response = $this->postJson("/api/v1/orders/{$order->id}/items", [
        'product_id' => $this->product->id,
        'name' => $this->product->name,
        'quantity' => 3,
        'unit_price' => 100,
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'success',
            'message',
            'data',
        ]);

    $this->assertDatabaseHas('order_items', [
        'order_id' => $order->id,
        'product_id' => $this->product->id,
    ]);
});

it('can remove an item from an order', function () {
    $order = Order::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'draft',
    ]);

    $item = OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $this->product->id,
    ]);

    $response = $this->deleteJson("/api/v1/orders/{$order->id}/items/{$item->id}");

    $response->assertStatus(200);

    $this->assertDatabaseMissing('order_items', ['id' => $item->id]);
});
