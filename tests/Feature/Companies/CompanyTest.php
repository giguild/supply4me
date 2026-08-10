<?php

use App\Models\Companies\Company;
use App\Models\Core\User;
use Laravel\Sanctum\Sanctum;

it('can list companies', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    Company::factory()->count(3)->create();

    $response = $this->getJson('/api/v1/companies');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data',
            'meta',
        ]);
});

it('can create a company', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $companyData = [
        'name' => 'New Company',
        'email' => 'newcompany@example.com',
        'phone' => '1234567890',
        'address' => '123 Main St',
        'city' => 'New York',
        'state' => 'NY',
        'country' => 'US',
        'postal_code' => '10001',
        'tax_number' => 'TAX123',
        'registration_number' => 'REG123',
    ];

    $response = $this->postJson('/api/v1/companies', $companyData);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'success',
            'message',
            'data',
        ]);

    $this->assertDatabaseHas('companies', [
        'name' => 'New Company',
        'email' => 'newcompany@example.com',
    ]);
});

it('can show a company', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $company = Company::factory()->create();

    $response = $this->getJson("/api/v1/companies/{$company->id}");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data',
        ]);
});

it('can update a company', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $company = Company::factory()->create();

    $response = $this->putJson("/api/v1/companies/{$company->id}", [
        'name' => 'Updated Company',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Company updated successfully',
        ]);

    $this->assertDatabaseHas('companies', [
        'id' => $company->id,
        'name' => 'Updated Company',
    ]);
});

it('can delete a company', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $company = Company::factory()->create();

    $response = $this->deleteJson("/api/v1/companies/{$company->id}");

    $response->assertStatus(200);

    $this->assertSoftDeleted('companies', [
        'id' => $company->id,
    ]);
});

it('validates company creation fields', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/v1/companies', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'email']);
});

it('validates email uniqueness on create', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    Company::factory()->create(['email' => 'existing@example.com']);

    $response = $this->postJson('/api/v1/companies', [
        'name' => 'Test',
        'email' => 'existing@example.com',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('validates email uniqueness on update excluding self', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $company = Company::factory()->create(['email' => 'original@example.com']);

    $response = $this->putJson("/api/v1/companies/{$company->id}", [
        'email' => 'original@example.com',
    ]);

    $response->assertStatus(200);
});

it('validates email uniqueness on update for other companies', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $company1 = Company::factory()->create(['email' => 'company1@example.com']);
    $company2 = Company::factory()->create(['email' => 'company2@example.com']);

    $response = $this->putJson("/api/v1/companies/{$company1->id}", [
        'email' => 'company2@example.com',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('requires authentication to access companies', function () {
    $response = $this->getJson('/api/v1/companies');

    $response->assertStatus(401);
});

it('can get company settings', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $company = Company::factory()->create();

    $response = $this->getJson("/api/v1/companies/{$company->id}/settings");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data',
        ]);
});

it('can update company settings', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $company = Company::factory()->create();

    $response = $this->putJson("/api/v1/companies/{$company->id}/settings", [
        'currency' => 'EUR',
        'timezone' => 'Europe/London',
        'tax_rate' => 20.0,
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Company settings updated successfully',
        ]);
});
