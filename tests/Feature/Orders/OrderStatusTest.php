<?php

use App\Models\Core\User;
use App\Models\Customers\Customer;
use App\Models\Orders\Order;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
    $this->customer = Customer::factory()->create(['company_id' => $this->user->company_id]);
});

it('can transition order from draft to pending', function () {
    $order = Order::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'draft',
    ]);

    $response = $this->postJson("/api/v1/orders/{$order->id}/confirm");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Order confirmed successfully',
        ]);

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'status' => 'pending',
    ]);
});

it('can cancel an order', function () {
    $order = Order::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'pending',
    ]);

    $response = $this->postJson("/api/v1/orders/{$order->id}/cancel", [
        'reason' => 'Customer requested cancellation',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Order cancelled successfully',
        ]);

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'status' => 'cancelled',
    ]);
});

it('cannot cancel a completed order', function () {
    $order = Order::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'completed',
    ]);

    $response = $this->postJson("/api/v1/orders/{$order->id}/cancel", [
        'reason' => 'Too late',
    ]);

    $response->assertStatus(422);
});

it('can hold an order', function () {
    $order = Order::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'pending',
    ]);

    $response = $this->postJson("/api/v1/orders/{$order->id}/hold");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
        ]);

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'status' => 'on_hold',
    ]);
});

it('can release an order from hold', function () {
    $order = Order::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'on_hold',
    ]);

    $response = $this->postJson("/api/v1/orders/{$order->id}/release");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
        ]);

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'status' => 'processing',
    ]);
});

it('cannot hold a cancelled order', function () {
    $order = Order::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'cancelled',
    ]);

    $response = $this->postJson("/api/v1/orders/{$order->id}/hold");

    $response->assertStatus(422);
});

it('tracks order status history', function () {
    $order = Order::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'draft',
    ]);

    $this->postJson("/api/v1/orders/{$order->id}/confirm");

    $this->assertDatabaseHas('order_status_histories', [
        'order_id' => $order->id,
        'from_state' => 'draft',
        'to_state' => 'pending',
    ]);
});
