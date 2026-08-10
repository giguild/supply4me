<?php

use App\Models\Core\User;
use App\Models\Customers\Customer;
use App\Models\Invoicing\Invoice;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
    $this->customer = Customer::factory()->create(['company_id' => $this->user->company_id]);
});

it('can send a draft invoice', function () {
    $invoice = Invoice::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'draft',
    ]);

    $response = $this->postJson("/api/v1/invoices/{$invoice->id}/send");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Invoice sent successfully',
        ]);

    $this->assertDatabaseHas('invoices', [
        'id' => $invoice->id,
        'status' => 'sent',
    ]);
});

it('cannot send an already sent invoice', function () {
    $invoice = Invoice::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'sent',
    ]);

    $response = $this->postJson("/api/v1/invoices/{$invoice->id}/send");

    $response->assertStatus(422);
});

it('cannot send a voided invoice', function () {
    $invoice = Invoice::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'void',
    ]);

    $response = $this->postJson("/api/v1/invoices/{$invoice->id}/send");

    $response->assertStatus(422);
});

it('can void a draft invoice', function () {
    $invoice = Invoice::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'draft',
    ]);

    $response = $this->postJson("/api/v1/invoices/{$invoice->id}/void", [
        'reason' => 'Invoice created in error',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Invoice voided successfully',
        ]);

    $this->assertDatabaseHas('invoices', [
        'id' => $invoice->id,
        'status' => 'void',
    ]);
});

it('can void a pending invoice', function () {
    $invoice = Invoice::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'pending',
    ]);

    $response = $this->postJson("/api/v1/invoices/{$invoice->id}/void", [
        'reason' => 'Customer cancelled',
    ]);

    $response->assertStatus(200);

    $this->assertDatabaseHas('invoices', [
        'id' => $invoice->id,
        'status' => 'void',
    ]);
});

it('requires a reason for voiding', function () {
    $invoice = Invoice::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'draft',
    ]);

    $response = $this->postJson("/api/v1/invoices/{$invoice->id}/void", []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['reason']);
});

it('cannot void a paid invoice', function () {
    $invoice = Invoice::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'paid',
    ]);

    $response = $this->postJson("/api/v1/invoices/{$invoice->id}/void", [
        'reason' => 'Too late',
    ]);

    $response->assertStatus(422);
});

it('tracks invoice status history on transitions', function () {
    $invoice = Invoice::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'draft',
    ]);

    $this->postJson("/api/v1/invoices/{$invoice->id}/send");

    $this->assertDatabaseHas('invoice_status_histories', [
        'invoice_id' => $invoice->id,
        'from_state' => 'draft',
        'to_state' => 'sent',
    ]);
});
