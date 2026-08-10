<?php

use App\Models\Core\User;
use App\Models\Delivery\Delivery;
use App\Models\Delivery\Driver;
use App\Models\Customers\Customer;
use App\Models\Orders\Order;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
    $this->customer = Customer::factory()->create(['company_id' => $this->user->company_id]);
    $this->order = Order::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
    ]);
    $this->driver = Driver::factory()->create();
});

it('can assign a driver to a delivery', function () {
    $delivery = Delivery::factory()->create([
        'order_id' => $this->order->id,
        'status' => 'pending',
    ]);

    $response = $this->postJson("/api/v1/deliveries/{$delivery->id}/assign-driver", [
        'driver_id' => $this->driver->id,
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Driver assigned successfully',
        ]);

    $this->assertDatabaseHas('deliveries', [
        'id' => $delivery->id,
        'driver_id' => $this->driver->id,
    ]);
});

it('validates driver exists for assignment', function () {
    $delivery = Delivery::factory()->create([
        'order_id' => $this->order->id,
        'status' => 'pending',
    ]);

    $response = $this->postJson("/api/v1/deliveries/{$delivery->id}/assign-driver", [
        'driver_id' => 99999,
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['driver_id']);
});

it('can list drivers', function () {
    Driver::factory()->count(3)->create();

    $response = $this->getJson('/api/v1/drivers');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data',
            'meta',
        ]);
});

it('can create a driver', function () {
    $response = $this->postJson('/api/v1/drivers', [
        'name' => 'New Driver',
        'phone' => '1234567890',
        'email' => 'driver@example.com',
        'license_number' => 'DL-12345',
        'vehicle_type' => 'Truck',
        'vehicle_registration' => 'ABC-123',
        'status' => 'active',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'success',
            'message',
            'data',
        ]);

    $this->assertDatabaseHas('drivers', [
        'name' => 'New Driver',
        'license_number' => 'DL-12345',
    ]);
});

it('validates required fields for driver creation', function () {
    $response = $this->postJson('/api/v1/drivers', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors([
            'name',
            'phone',
            'license_number',
        ]);
});

it('can update a driver', function () {
    $driver = Driver::factory()->create();

    $response = $this->putJson("/api/v1/drivers/{$driver->id}", [
        'name' => 'Updated Driver',
        'status' => 'inactive',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Driver updated successfully',
        ]);
});

it('can delete a driver without active deliveries', function () {
    $driver = Driver::factory()->create();

    $response = $this->deleteJson("/api/v1/drivers/{$driver->id}");

    $response->assertStatus(200);

    $this->assertDatabaseMissing('drivers', ['id' => $driver->id]);
});

it('cannot delete a driver with active deliveries', function () {
    $driver = Driver::factory()->create();

    Delivery::factory()->create([
        'driver_id' => $driver->id,
        'order_id' => $this->order->id,
        'status' => 'out_for_delivery',
    ]);

    $response = $this->deleteJson("/api/v1/drivers/{$driver->id}");

    $response->assertStatus(422);
});

it('can show a driver', function () {
    $driver = Driver::factory()->create();

    $response = $this->getJson("/api/v1/drivers/{$driver->id}");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data',
        ]);
});
