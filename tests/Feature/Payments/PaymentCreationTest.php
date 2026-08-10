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
    $this->order = Order::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'total_amount' => 500,
    ]);
});

it('can create a payment', function () {
    $response = $this->postJson('/api/v1/payments', [
        'customer_id' => $this->customer->id,
        'order_id' => $this->order->id,
        'amount' => 250,
        'payment_method' => 'bank_transfer',
        'payment_date' => now()->toDateString(),
        'reference' => 'TXN-001',
        'notes' => 'Partial payment',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'success',
            'message',
            'data',
        ]);

    $this->assertDatabaseHas('payments', [
        'customer_id' => $this->customer->id,
        'order_id' => $this->order->id,
        'amount' => 250,
        'status' => 'pending',
    ]);
});

it('validates required payment fields', function () {
    $response = $this->postJson('/api/v1/payments', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors([
            'customer_id',
            'amount',
            'payment_method',
            'payment_date',
        ]);
});

it('validates payment amount is positive', function () {
    $response = $this->postJson('/api/v1/payments', [
        'customer_id' => $this->customer->id,
        'amount' => -50,
        'payment_method' => 'cash',
        'payment_date' => now()->toDateString(),
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['amount']);
});

it('validates payment method is valid', function () {
    $response = $this->postJson('/api/v1/payments', [
        'customer_id' => $this->customer->id,
        'amount' => 100,
        'payment_method' => 'invalid_method',
        'payment_date' => now()->toDateString(),
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['payment_method']);
});

it('can show a payment', function () {
    $payment = Payment::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
    ]);

    $response = $this->getJson("/api/v1/payments/{$payment->id}");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data',
        ]);
});

it('can list payments with filters', function () {
    Payment::factory()->count(5)->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'pending',
    ]);

    $response = $this->getJson('/api/v1/payments?status=pending');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data',
            'meta',
        ]);
});

it('can create a payment without order', function () {
    $response = $this->postJson('/api/v1/payments', [
        'customer_id' => $this->customer->id,
        'amount' => 500,
        'payment_method' => 'cash',
        'payment_date' => now()->toDateString(),
        'notes' => 'General payment',
    ]);

    $response->assertStatus(201);

    $this->assertDatabaseHas('payments', [
        'customer_id' => $this->customer->id,
        'order_id' => null,
        'amount' => 500,
    ]);
});

it('creates payment with correct status', function () {
    $response = $this->postJson('/api/v1/payments', [
        'customer_id' => $this->customer->id,
        'order_id' => $this->order->id,
        'amount' => 100,
        'payment_method' => 'bank_transfer',
        'payment_date' => now()->toDateString(),
    ]);

    $response->assertStatus(201);

    $payment = Payment::where('customer_id', $this->customer->id)->first();
    expect($payment->status)->toBe('pending');
});
