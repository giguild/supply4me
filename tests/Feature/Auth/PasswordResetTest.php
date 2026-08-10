<?php

use App\Models\Core\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

it('sends a password reset link for existing user', function () {
    $user = User::factory()->create();

    Password::shouldReceive('createToken')->once()->andReturn('test-token');
    Password::shouldReceive('sendResetLink')->once()->andReturn(Password::RESET_LINK_SENT);

    $response = $this->postJson('/api/v1/auth/forgot-password', [
        'email' => $user->email,
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
        ]);
});

it('validates email field is required', function () {
    $response = $this->postJson('/api/v1/auth/forgot-password', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('validates email format', function () {
    $response = $this->postJson('/api/v1/auth/forgot-password', [
        'email' => 'not-an-email',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('returns error for non-existent email', function () {
    Password::shouldReceive('sendResetLink')->once()->andReturn(Password::INVALID_USER);

    $response = $this->postJson('/api/v1/auth/forgot-password', [
        'email' => 'nonexistent@example.com',
    ]);

    $response->assertStatus(400);
});
