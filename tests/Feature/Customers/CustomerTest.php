<?php

use App\Models\Customers\Customer;
use App\Models\Customers\CustomerContact;
use App\Models\Customers\CustomerShippingAddress;
use App\Models\Core\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

it('can list customers', function () {
    Customer::factory()->count(3)->create();

    $response = $this->getJson('/api/v1/customers');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data',
            'meta',
        ]);
});

it('can create a customer', function () {
    $customerData = [
        'name' => 'New Customer',
        'code' => 'CUST001',
        'email' => 'customer@example.com',
        'phone' => '1234567890',
        'type' => 'business',
        'credit_limit' => 10000,
        'payment_terms' => 'Net 30',
    ];

    $response = $this->postJson('/api/v1/customers', $customerData);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'success',
            'message',
            'data',
        ]);

    $this->assertDatabaseHas('customers', [
        'name' => 'New Customer',
        'email' => 'customer@example.com',
    ]);
});

it('can show a customer with contacts and addresses', function () {
    $customer = Customer::factory()->create();

    CustomerContact::factory()->count(2)->create(['customer_id' => $customer->id]);
    CustomerShippingAddress::factory()->count(2)->create(['customer_id' => $customer->id]);

    $response = $this->getJson("/api/v1/customers/{$customer->id}");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data',
        ]);
});

it('can update a customer', function () {
    $customer = Customer::factory()->create();

    $response = $this->putJson("/api/v1/customers/{$customer->id}", [
        'name' => 'Updated Customer',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Customer updated successfully',
        ]);

    $this->assertDatabaseHas('customers', [
        'id' => $customer->id,
        'name' => 'Updated Customer',
    ]);
});

it('can delete a customer', function () {
    $customer = Customer::factory()->create();

    $response = $this->deleteJson("/api/v1/customers/{$customer->id}");

    $response->assertStatus(200);

    $this->assertSoftDeleted('customers', [
        'id' => $customer->id,
    ]);
});

it('validates customer creation fields', function () {
    $response = $this->postJson('/api/v1/customers', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'code', 'email']);
});

it('can add a contact to a customer', function () {
    $customer = Customer::factory()->create();

    $response = $this->postJson("/api/v1/customers/{$customer->id}/contacts", [
        'name' => 'Contact Person',
        'email' => 'contact@example.com',
        'phone' => '1234567890',
        'position' => 'Manager',
        'is_primary' => true,
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'success',
            'message',
            'data',
        ]);

    $this->assertDatabaseHas('customer_contacts', [
        'customer_id' => $customer->id,
        'name' => 'Contact Person',
        'is_primary' => true,
    ]);
});

it('can update a customer contact', function () {
    $customer = Customer::factory()->create();
    $contact = CustomerContact::factory()->create(['customer_id' => $customer->id]);

    $response = $this->putJson("/api/v1/customers/{$customer->id}/contacts/{$contact->id}", [
        'name' => 'Updated Contact',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Contact updated successfully',
        ]);
});

it('can delete a customer contact', function () {
    $customer = Customer::factory()->create();
    $contact = CustomerContact::factory()->create(['customer_id' => $customer->id]);

    $response = $this->deleteJson("/api/v1/customers/{$customer->id}/contacts/{$contact->id}");

    $response->assertStatus(200);

    $this->assertDatabaseMissing('customer_contacts', [
        'id' => $contact->id,
    ]);
});

it('can add an address to a customer', function () {
    $customer = Customer::factory()->create();

    $response = $this->postJson("/api/v1/customers/{$customer->id}/addresses", [
        'label' => 'Main Office',
        'address_line_1' => '123 Main Street',
        'city' => 'New York',
        'state' => 'NY',
        'country' => 'US',
        'postal_code' => '10001',
        'is_default' => true,
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'success',
            'message',
            'data',
        ]);

    $this->assertDatabaseHas('customer_shipping_addresses', [
        'customer_id' => $customer->id,
        'label' => 'Main Office',
        'is_default' => true,
    ]);
});

it('can update a customer address', function () {
    $customer = Customer::factory()->create();
    $address = CustomerShippingAddress::factory()->create(['customer_id' => $customer->id]);

    $response = $this->putJson("/api/v1/customers/{$customer->id}/addresses/{$address->id}", [
        'label' => 'Updated Address',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Address updated successfully',
        ]);
});

it('can delete a customer address', function () {
    $customer = Customer::factory()->create();
    $address = CustomerShippingAddress::factory()->create(['customer_id' => $customer->id]);

    $response = $this->deleteJson("/api/v1/customers/{$customer->id}/addresses/{$address->id}");

    $response->assertStatus(200);

    $this->assertDatabaseMissing('customer_shipping_addresses', [
        'id' => $address->id,
    ]);
});

it('prevents contact access from wrong customer', function () {
    $customer1 = Customer::factory()->create();
    $customer2 = Customer::factory()->create();
    $contact = CustomerContact::factory()->create(['customer_id' => $customer1->id]);

    $response = $this->putJson("/api/v1/customers/{$customer2->id}/contacts/{$contact->id}", [
        'name' => 'Hacked',
    ]);

    $response->assertStatus(403);
});

it('prevents address access from wrong customer', function () {
    $customer1 = Customer::factory()->create();
    $customer2 = Customer::factory()->create();
    $address = CustomerShippingAddress::factory()->create(['customer_id' => $customer1->id]);

    $response = $this->deleteJson("/api/v1/customers/{$customer2->id}/addresses/{$address->id}");

    $response->assertStatus(403);
});

it('validates address required fields', function () {
    $customer = Customer::factory()->create();

    $response = $this->postJson("/api/v1/customers/{$customer->id}/addresses", []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors([
            'address_line_1',
            'city',
            'state',
            'country',
            'postal_code',
        ]);
});

it('can get customer credit status', function () {
    $customer = Customer::factory()->create([
        'credit_limit' => 50000,
        'credit_used' => 20000,
    ]);

    $response = $this->getJson("/api/v1/customers/{$customer->id}/credit-status");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data' => [
                'credit_limit',
                'credit_used',
                'credit_available',
                'credit_status',
            ],
        ]);
});
