<?php

namespace Tests;

use App\Models\Core\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Create a user for testing.
     */
    protected function createUser(array $attributes = []): User
    {
        return User::factory()->create($attributes);
    }

    /**
     * Create an authenticated user for testing.
     */
    protected function actingAsUser(array $attributes = []): User
    {
        $user = $this->createUser($attributes);
        $this->actingAs($user);

        return $user;
    }
}
