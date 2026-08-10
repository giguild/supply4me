<?php

use App\ValueObjects\Money;

it('creates a money value object', function () {
    $money = Money::from(100, 'USD');

    expect($money->getAmount())->toBe(100.00)
        ->and($money->getCurrency())->toBe('USD');
});

it('creates zero money', function () {
    $money = Money::zero('USD');

    expect($money->getAmount())->toBe(0.00)
        ->and($money->isZero())->toBeTrue();
});

it('adds two money values', function () {
    $a = Money::from(100, 'USD');
    $b = Money::from(50, 'USD');

    $result = $a->add($b);

    expect($result->getAmount())->toBe(150.00);
});

it('subtracts two money values', function () {
    $a = Money::from(100, 'USD');
    $b = Money::from(30, 'USD');

    $result = $a->subtract($b);

    expect($result->getAmount())->toBe(70.00);
});

it('multiplies money by a factor', function () {
    $money = Money::from(100, 'USD');

    $result = $money->multiply(3);

    expect($result->getAmount())->toBe(300.00);
});

it('throws exception when adding different currencies', function () {
    $a = Money::from(100, 'USD');
    $b = Money::from(50, 'EUR');

    $this->expectException(\InvalidArgumentException::class);

    $a->add($b);
});

it('throws exception when subtracting different currencies', function () {
    $a = Money::from(100, 'USD');
    $b = Money::from(50, 'GBP');

    $this->expectException(\InvalidArgumentException::class);

    $a->subtract($b);
});

it('compares two equal money values', function () {
    $a = Money::from(100, 'USD');
    $b = Money::from(100, 'USD');

    expect($a->equals($b))->toBeTrue();
});

it('compares two different money values', function () {
    $a = Money::from(100, 'USD');
    $b = Money::from(200, 'USD');

    expect($a->equals($b))->toBeFalse();
});

it('checks if money is positive', function () {
    $money = Money::from(100, 'USD');

    expect($money->isPositive())->toBeTrue();
});

it('checks if money is negative', function () {
    $money = Money::from(-50, 'USD');

    expect($money->isNegative())->toBeTrue();
});

it('checks if money is zero', function () {
    $money = Money::from(0, 'USD');

    expect($money->isZero())->toBeTrue()
        ->and($money->isPositive())->toBeFalse()
        ->and($money->isNegative())->toBeFalse();
});

it('formats money with currency symbol', function () {
    $money = Money::from(1234.56, 'USD');

    expect($money->format())->toBe('$1,234.56');
});

it('formats EUR with euro symbol', function () {
    $money = Money::from(100, 'EUR');

    expect($money->format())->toBe('€100.00');
});

it('converts to array', function () {
    $money = Money::from(100, 'USD');

    $array = $money->toArray();

    expect($array)->toBe([
        'amount' => 100.00,
        'currency' => 'USD',
    ]);
});

it('converts to string', function () {
    $money = Money::from(100, 'USD');

    expect((string) $money)->toBe('$100.00');
});

it('rounds to two decimal places', function () {
    $money = Money::from(100.126, 'USD');

    expect($money->getAmount())->toBe(100.13);
});

it('normalizes currency to uppercase', function () {
    $money = Money::from(100, 'usd');

    expect($money->getCurrency())->toBe('USD');
});

it('handles negative amounts', function () {
    $money = Money::from(-50, 'USD');

    expect($money->getAmount())->toBe(-50.00)
        ->and($money->isNegative())->toBeTrue()
        ->and($money->isPositive())->toBeFalse();
});
