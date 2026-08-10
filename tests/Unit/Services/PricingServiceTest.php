<?php

use App\Services\Pricing\PricingService;
use App\Services\Pricing\DiscountService;
use App\Services\Pricing\TaxService;
use App\Models\Orders\OrderItem;
use App\Models\Products\Product;
use App\ValueObjects\Money;
use App\ValueObjects\Quantity;

it('calculates line total correctly', function () {
    $pricingService = app(PricingService::class);

    $product = Product::factory()->create(['selling_price' => 100]);

    $item = new OrderItem([
        'product_id' => $product->id,
        'quantity' => 5,
        'unit_price' => 100,
        'discount_percentage' => 0,
    ]);

    $total = $pricingService->calculateLineTotal($item);

    expect($total)->toBeInstanceOf(Money::class)
        ->and($total->getAmount())->toBe(500.00);
});

it('applies percentage discount correctly', function () {
    $pricingService = app(PricingService::class);

    $amount = Money::from(1000, 'USD');
    $discounted = $pricingService->applyDiscount($amount, 10);

    expect($discounted->getAmount())->toBe(900.00);
});

it('throws exception for invalid discount percentage', function () {
    $pricingService = app(PricingService::class);

    $amount = Money::from(1000, 'USD');

    $this->expectException(\InvalidArgumentException::class);

    $pricingService->applyDiscount($amount, 150);
});

it('throws exception for negative discount percentage', function () {
    $pricingService = app(PricingService::class);

    $amount = Money::from(1000, 'USD');

    $this->expectException(\InvalidArgumentException::class);

    $pricingService->applyDiscount($amount, -10);
});

it('calculates price from price list', function () {
    $pricingService = app(PricingService::class);

    $product = Product::factory()->create(['selling_price' => 100]);
    $quantity = Quantity::from(10);

    $price = $pricingService->getPrice($product, null, $quantity);

    expect($price)->toBeInstanceOf(Money::class)
        ->and($price->getAmount())->toBe(100.00);
});

it('applies 100% discount results in zero', function () {
    $pricingService = app(PricingService::class);

    $amount = Money::from(500, 'USD');
    $discounted = $pricingService->applyDiscount($amount, 100);

    expect($discounted->getAmount())->toBe(0.00);
});

it('applies 0% discount results in same amount', function () {
    $pricingService = app(PricingService::class);

    $amount = Money::from(500, 'USD');
    $discounted = $pricingService->applyDiscount($amount, 0);

    expect($discounted->getAmount())->toBe(500.00);
});
