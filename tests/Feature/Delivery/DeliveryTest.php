<?php

use App\Models\Core\User;
use App\Models\Customers\Customer;
use App\Models\Delivery\Delivery;
use App\Models\Delivery\Driver;
use App\Models\Orders\Order;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
    $this->customer = Customer::factory()->create(['company_id' => $this->user->company_id]);
    $this->order = Order::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'confirmed',
    ]);
    $this->driver = Driver::factory()->create();
});

it('can create a delivery', function () {
    $response = $this->postJson('/api/v1/deliveries', [
        'order_id' => $this->order->id,
        'scheduled_date' => now()->addDays(2)->toDateString(),
        'delivery_address' => [
            'line1' => '123 Delivery Street',
            'city' => 'Delivery City',
            'state' => 'DC',
            'country' => 'US',
            'postal_code' => '12345',
        ],
        'contact_name' => 'John Doe',
        'contact_phone' => '1234567890',
        'priority' => 'high',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'success',
            'message',
            'data',
        ]);

    $this->assertDatabaseHas('deliveries', [
        'order_id' => $this->order->id,
        'status' => 'pending',
    ]);
});

it('validates required fields for delivery', function () {
    $response = $this->postJson('/api/v1/deliveries', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors([
            'order_id',
            'scheduled_date',
            'delivery_address',
        ]);
});

it('can show a delivery', function () {
    $delivery = Delivery::factory()->create([
        'order_id' => $this->order->id,
    ]);

    $response = $this->getJson("/api/v1/deliveries/{$delivery->id}");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data',
        ]);
});

it('can list deliveries', function () {
    Delivery::factory()->count(3)->create([
        'order_id' => $this->order->id,
    ]);

    $response = $this->getJson('/api/v1/deliveries');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data',
            'meta',
        ]);
});

it('can start a delivery', function () {
    $delivery = Delivery::factory()->create([
        'order_id' => $this->order->id,
        'driver_id' => $this->driver->id,
        'status' => 'assigned',
    ]);

    $response = $this->postJson("/api/v1/deliveries/{$delivery->id}/start");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Delivery started',
        ]);

    $this->assertDatabaseHas('deliveries', [
        'id' => $delivery->id,
        'status' => 'out_for_delivery',
    ]);
});

it('can complete a delivery', function () {
    $delivery = Delivery::factory()->create([
        'order_id' => $this->order->id,
        'driver_id' => $this->driver->id,
        'status' => 'out_for_delivery',
    ]);

    $response = $this->postJson("/api/v1/deliveries/{$delivery->id}/complete", [
        'proof_of_delivery' => 'Signed by customer',
        'recipient_name' => 'John Doe',
        'notes' => 'Delivered to front door',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Delivery completed successfully',
        ]);

    $this->assertDatabaseHas('deliveries', [
        'id' => $delivery->id,
        'status' => 'delivered',
    ]);
});

it('can record a failed delivery', function () {
    $delivery = Delivery::factory()->create([
        'order_id' => $this->order->id,
        'driver_id' => $this->driver->id,
        'status' => 'out_for_delivery',
    ]);

    $response = $this->postJson("/api/v1/deliveries/{$delivery->id}/fail", [
        'reason' => 'Customer not available',
        'condition' => 'customer_unavailable',
        'notes' => 'Left a calling card',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Failed delivery recorded',
        ]);

    $this->assertDatabaseHas('deliveries', [
        'id' => $delivery->id,
        'status' => 'failed',
    ]);
});

it('validates failure reason is required', function () {
    $delivery = Delivery::factory()->create([
        'order_id' => $this->order->id,
        'driver_id' => $this->driver->id,
        'status' => 'out_for_delivery',
    ]);

    $response = $this->postJson("/api/v1/deliveries/{$delivery->id}/fail", []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['reason']);
});
