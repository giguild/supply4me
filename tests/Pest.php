<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The base TestCase for the Supply4Me application.
|
*/

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Expect Helpers
|--------------------------------------------------------------------------
|
| Custom Pest expectation helpers for the Supply4Me tests.
|
*/

expect()->extend('toBeValid', function () {
    return $this->toBe(true);
});

expect()->extend('toBeInvalid', function () {
    return $this->toBe(false);
});
