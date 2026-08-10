<?php

use App\Models\Core\User;
use App\Models\Customers\Customer;
use App\Models\Payments\Payment;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
    $this->customer = Customer::factory()->create(['company_id' => $this->user->company_id]);
});

it('can approve a pending payment', function () {
    $payment = Payment::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'pending',
    ]);

    $response = $this->postJson("/api/v1/payments/{$payment->id}/approve");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Payment approved successfully',
        ]);

    $this->assertDatabaseHas('payments', [
        'id' => $payment->id,
        'status' => 'completed',
        'approved_by' => $this->user->id,
    ]);
});

it('cannot approve a completed payment', function () {
    $payment = Payment::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'completed',
    ]);

    $response = $this->postJson("/api/v1/payments/{$payment->id}/approve");

    $response->assertStatus(422);
});

it('can reject a pending payment', function () {
    $payment = Payment::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'pending',
    ]);

    $response = $this->postJson("/api/v1/payments/{$payment->id}/reject", [
        'reason' => 'Invalid payment method',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Payment rejected successfully',
        ]);

    $this->assertDatabaseHas('payments', [
        'id' => $payment->id,
        'status' => 'cancelled',
    ]);
});

it('requires a reason for rejection', function () {
    $payment = Payment::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'pending',
    ]);

    $response = $this->postJson("/api/v1/payments/{$payment->id}/reject", []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['reason']);
});

it('can refund a completed payment', function () {
    $payment = Payment::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'completed',
        'amount' => 500,
    ]);

    $response = $this->postJson("/api/v1/payments/{$payment->id}/refund", [
        'amount' => 200,
        'reason' => 'Partial refund for returned items',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Payment refunded successfully',
        ]);

    $this->assertDatabaseHas('payments', [
        'id' => $payment->id,
        'status' => 'refunded',
    ]);
});

it('requires refund amount and reason', function () {
    $payment = Payment::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'completed',
    ]);

    $response = $this->postJson("/api/v1/payments/{$payment->id}/refund", []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['amount', 'reason']);
});

it('validates refund amount is positive', function () {
    $payment = Payment::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'completed',
    ]);

    $response = $this->postJson("/api/v1/payments/{$payment->id}/refund", [
        'amount' => -100,
        'reason' => 'Invalid amount',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['amount']);
});

it('approval sets approved_by and cleared_date', function () {
    $payment = Payment::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'pending',
    ]);

    $this->postJson("/api/v1/payments/{$payment->id}/approve");

    $payment->refresh();

    expect($payment->approved_by)->toBe($this->user->id)
        ->and($payment->cleared_date)->not->toBeNull();
});
