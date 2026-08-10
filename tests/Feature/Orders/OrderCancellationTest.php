<?php

use App\Models\Core\User;
use App\Models\Customers\Customer;
use App\Models\Orders\Order;
use App\Models\Payments\Payment;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
    $this->customer = Customer::factory()->create(['company_id' => $this->user->company_id]);
});

it('can cancel a pending order', function () {
    $order = Order::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'pending',
    ]);

    $response = $this->postJson("/api/v1/orders/{$order->id}/cancel", [
        'reason' => 'Changed my mind',
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

it('requires a reason for cancellation', function () {
    $order = Order::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'pending',
    ]);

    $response = $this->postJson("/api/v1/orders/{$order->id}/cancel", []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['reason']);
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

it('cannot cancel an already cancelled order', function () {
    $order = Order::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'cancelled',
    ]);

    $response = $this->postJson("/api/v1/orders/{$order->id}/cancel", [
        'reason' => 'Double cancel',
    ]);

    $response->assertStatus(422);
});

it('can cancel an order with associated payment', function () {
    $order = Order::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'pending',
    ]);

    Payment::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'order_id' => $order->id,
        'status' => 'pending',
    ]);

    $response = $this->postJson("/api/v1/orders/{$order->id}/cancel", [
        'reason' => 'Payment issue',
    ]);

    $response->assertStatus(200);

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'status' => 'cancelled',
    ]);
});

it('cancellation records status history', function () {
    $order = Order::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'pending',
    ]);

    $this->postJson("/api/v1/orders/{$order->id}/cancel", [
        'reason' => 'Customer request',
    ]);

    $this->assertDatabaseHas('order_status_histories', [
        'order_id' => $order->id,
        'to_state' => 'cancelled',
    ]);
});
