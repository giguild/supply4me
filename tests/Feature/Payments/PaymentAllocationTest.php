<?php

use App\Models\Core\User;
use App\Models\Customers\Customer;
use App\Models\Invoicing\Invoice;
use App\Models\Payments\Payment;
use App\Models\Payments\PaymentAllocation;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
    $this->customer = Customer::factory()->create(['company_id' => $this->user->company_id]);
    $this->invoice = Invoice::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'total_amount' => 1000,
        'balance_due' => 1000,
    ]);
});

it('can allocate a payment to an invoice', function () {
    $payment = Payment::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'completed',
        'amount' => 500,
    ]);

    $response = $this->postJson("/api/v1/payments/{$payment->id}/allocations", [
        'invoice_id' => $this->invoice->id,
        'amount' => 500,
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'success',
            'message',
            'data',
        ]);

    $this->assertDatabaseHas('payment_allocations', [
        'payment_id' => $payment->id,
        'invoice_id' => $this->invoice->id,
        'amount' => 500,
    ]);
});

it('validates allocation amount is positive', function () {
    $payment = Payment::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'completed',
        'amount' => 500,
    ]);

    $response = $this->postJson("/api/v1/payments/{$payment->id}/allocations", [
        'invoice_id' => $this->invoice->id,
        'amount' => -100,
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['amount']);
});

it('validates invoice exists for allocation', function () {
    $payment = Payment::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'completed',
        'amount' => 500,
    ]);

    $response = $this->postJson("/api/v1/payments/{$payment->id}/allocations", [
        'invoice_id' => 99999,
        'amount' => 500,
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['invoice_id']);
});

it('can list payment allocations', function () {
    $payment = Payment::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
    ]);

    PaymentAllocation::factory()->count(3)->create([
        'payment_id' => $payment->id,
        'invoice_id' => $this->invoice->id,
    ]);

    $response = $this->getJson("/api/v1/payments/{$payment->id}/allocations");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data',
        ]);
});

it('can delete a payment allocation', function () {
    $payment = Payment::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
    ]);

    $allocation = PaymentAllocation::factory()->create([
        'payment_id' => $payment->id,
        'invoice_id' => $this->invoice->id,
    ]);

    $response = $this->deleteJson("/api/v1/payments/{$payment->id}/allocations/{$allocation->id}");

    $response->assertStatus(200);

    $this->assertDatabaseMissing('payment_allocations', ['id' => $allocation->id]);
});

it('updates invoice balance after allocation', function () {
    $payment = Payment::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'completed',
        'amount' => 300,
    ]);

    $this->postJson("/api/v1/payments/{$payment->id}/allocations", [
        'invoice_id' => $this->invoice->id,
        'amount' => 300,
    ]);

    $this->invoice->refresh();

    expect($this->invoice->amount_paid)->toBe('300.00')
        ->and($this->invoice->balance_due)->toBe('700.00');
});
