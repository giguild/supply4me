<?php

use App\Models\Core\User;
use App\Models\Companies\Company;
use Illuminate\Support\Facades\Hash;

it('allows a user to register successfully', function () {
    $companyData = [
        'name' => 'Test Company',
        'email' => 'company@example.com',
        'phone' => '1234567890',
        'address' => '123 Test Street',
        'city' => 'Test City',
        'state' => 'Test State',
        'country' => 'Test Country',
        'postal_code' => '12345',
        'owner_name' => 'John Doe',
        'owner_email' => 'john@example.com',
        'owner_password' => 'password123',
        'owner_password_confirmation' => 'password123',
    ];

    $response = $this->postJson('/api/v1/auth/register', $companyData);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => ['token', 'user'],
        ]);

    $this->assertDatabaseHas('companies', [
        'name' => 'Test Company',
        'email' => 'company@example.com',
    ]);

    $this->assertDatabaseHas('users', [
        'email' => 'john@example.com',
        'name' => 'John Doe',
    ]);
});

it('validates required fields for registration', function () {
    $response = $this->postJson('/api/v1/auth/register', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors([
            'name',
            'email',
            'owner_name',
            'owner_email',
            'owner_password',
        ]);
});

it('validates email uniqueness for company', function () {
    Company::factory()->create(['email' => 'existing@example.com']);

    $response = $this->postJson('/api/v1/auth/register', [
        'name' => 'Test Company',
        'email' => 'existing@example.com',
        'owner_name' => 'John Doe',
        'owner_email' => 'john@example.com',
        'owner_password' => 'password123',
        'owner_password_confirmation' => 'password123',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('validates email uniqueness for owner', function () {
    User::factory()->create(['email' => 'existing@example.com']);

    $response = $this->postJson('/api/v1/auth/register', [
        'name' => 'Test Company',
        'email' => 'company@example.com',
        'owner_name' => 'John Doe',
        'owner_email' => 'existing@example.com',
        'owner_password' => 'password123',
        'owner_password_confirmation' => 'password123',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['owner_email']);
});

it('validates password confirmation matches', function () {
    $response = $this->postJson('/api/v1/auth/register', [
        'name' => 'Test Company',
        'email' => 'company@example.com',
        'owner_name' => 'John Doe',
        'owner_email' => 'john@example.com',
        'owner_password' => 'password123',
        'owner_password_confirmation' => 'different-password',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['owner_password']);
});

it('validates email format for owner', function () {
    $response = $this->postJson('/api/v1/auth/register', [
        'name' => 'Test Company',
        'email' => 'company@example.com',
        'owner_name' => 'John Doe',
        'owner_email' => 'not-an-email',
        'owner_password' => 'password123',
        'owner_password_confirmation' => 'password123',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['owner_email']);
});

it('creates a default company settings after registration', function () {
    $response = $this->postJson('/api/v1/auth/register', [
        'name' => 'Test Company',
        'email' => 'company@example.com',
        'owner_name' => 'John Doe',
        'owner_email' => 'john@example.com',
        'owner_password' => 'password123',
        'owner_password_confirmation' => 'password123',
    ]);

    $response->assertStatus(201);

    $company = Company::where('email', 'company@example.com')->first();
    $this->assertNotNull($company);
});
