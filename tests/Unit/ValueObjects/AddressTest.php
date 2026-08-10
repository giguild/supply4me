<?php

use App\ValueObjects\Address;

it('creates an address value object', function () {
    $address = new Address(
        street: '123 Main Street',
        city: 'New York',
        state: 'NY',
        postalCode: '10001',
        country: 'US'
    );

    expect($address->getStreet())->toBe('123 Main Street')
        ->and($address->getCity())->toBe('New York')
        ->and($address->getState())->toBe('NY')
        ->and($address->getPostalCode())->toBe('10001')
        ->and($address->getCountry())->toBe('US');
});

it('creates address from array', function () {
    $address = Address::fromArray([
        'street' => '456 Oak Avenue',
        'city' => 'Los Angeles',
        'state' => 'CA',
        'postal_code' => '90001',
        'country' => 'US',
        'latitude' => 34.0522,
        'longitude' => -118.2437,
    ]);

    expect($address->getStreet())->toBe('456 Oak Avenue')
        ->and($address->getCity())->toBe('Los Angeles')
        ->and($address->getState())->toBe('CA')
        ->and($address->getPostalCode())->toBe('90001')
        ->and($address->getCountry())->toBe('US')
        ->and($address->getLatitude())->toBe(34.0522)
        ->and($address->getLongitude())->toBe(-118.2437);
});

it('normalizes country to uppercase', function () {
    $address = new Address(
        street: '123 Main Street',
        city: 'New York',
        state: 'NY',
        postalCode: '10001',
        country: 'us'
    );

    expect($address->getCountry())->toBe('US');
});

it('converts to array', function () {
    $address = new Address(
        street: '123 Main Street',
        city: 'New York',
        state: 'NY',
        postalCode: '10001',
        country: 'US',
        latitude: 40.7128,
        longitude: -74.0060
    );

    $array = $address->toArray();

    expect($array)->toBe([
        'street' => '123 Main Street',
        'city' => 'New York',
        'state' => 'NY',
        'postal_code' => '10001',
        'country' => 'US',
        'latitude' => 40.7128,
        'longitude' => -74.0060,
    ]);
});

it('converts to string', function () {
    $address = new Address(
        street: '123 Main Street',
        city: 'New York',
        state: 'NY',
        postalCode: '10001',
        country: 'US'
    );

    expect((string) $address)->toBe('123 Main Street, New York, NY, 10001, US');
});

it('validates a complete address', function () {
    $address = new Address(
        street: '123 Main Street',
        city: 'New York',
        state: 'NY',
        postalCode: '10001',
        country: 'US'
    );

    expect($address->isValid())->toBeTrue();
});

it('invalidates address with empty street', function () {
    $address = new Address(
        street: '',
        city: 'New York',
        state: 'NY',
        postalCode: '10001',
        country: 'US'
    );

    expect($address->isValid())->toBeFalse();
});

it('invalidates address with empty city', function () {
    $address = new Address(
        street: '123 Main Street',
        city: '',
        state: 'NY',
        postalCode: '10001',
        country: 'US'
    );

    expect($address->isValid())->toBeFalse();
});

it('compares two equal addresses', function () {
    $a = new Address('123 Main', 'NYC', 'NY', '10001', 'US');
    $b = new Address('123 Main', 'NYC', 'NY', '10001', 'US');

    expect($a->equals($b))->toBeTrue();
});

it('compares two different addresses', function () {
    $a = new Address('123 Main', 'NYC', 'NY', '10001', 'US');
    $b = new Address('456 Oak', 'LA', 'CA', '90001', 'US');

    expect($a->equals($b))->toBeFalse();
});

it('handles optional latitude and longitude', function () {
    $address = new Address(
        street: '123 Main Street',
        city: 'New York',
        state: 'NY',
        postalCode: '10001',
        country: 'US'
    );

    expect($address->getLatitude())->toBeNull()
        ->and($address->getLongitude())->toBeNull();
});

it('creates from array with defaults for missing fields', function () {
    $address = Address::fromArray([]);

    expect($address->getStreet())->toBe('')
        ->and($address->getCity())->toBe('')
        ->and($address->getCountry())->toBe('');
});
