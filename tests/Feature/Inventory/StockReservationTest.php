<?php

use App\Models\Core\User;
use App\Models\Inventory\StockItem;
use App\Models\Inventory\Warehouse;
use App\Models\Orders\Order;
use App\Models\Orders\OrderItem;
use App\Models\Products\Product;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
    $this->warehouse = Warehouse::factory()->create(['company_id' => $this->user->company_id]);
    $this->product = Product::factory()->create(['company_id' => $this->user->company_id]);
    $this->stockItem = StockItem::factory()->create([
        'company_id' => $this->user->company_id,
        'product_id' => $this->product->id,
        'warehouse_id' => $this->warehouse->id,
        'quantity_on_hand' => 100,
        'quantity_reserved' => 0,
    ]);
});

it('reserves stock for an order', function () {
    $order = Order::factory()->create([
        'company_id' => $this->user->company_id,
        'warehouse_id' => $this->warehouse->id,
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $this->product->id,
        'quantity' => 10,
    ]);

    $reservationService = app(\App\Services\Inventory\StockReservationService::class);
    $reservationService->reserveForOrder($order);

    $this->stockItem->refresh();

    expect($this->stockItem->quantity_reserved)->toBe(10.0)
        ->and($this->stockItem->quantity_on_hand)->toBe(100.0);
});

it('fails to reserve more than available stock', function () {
    $order = Order::factory()->create([
        'company_id' => $this->user->company_id,
        'warehouse_id' => $this->warehouse->id,
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $this->product->id,
        'quantity' => 200,
    ]);

    $reservationService = app(\App\Services\Inventory\StockReservationService::class);

    $this->expectException(\RuntimeException::class);

    $reservationService->reserveForOrder($order);
});

it('releases reserved stock for an order', function () {
    $order = Order::factory()->create([
        'company_id' => $this->user->company_id,
        'warehouse_id' => $this->warehouse->id,
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $this->product->id,
        'quantity' => 10,
    ]);

    $reservationService = app(\App\Services\Inventory\StockReservationService::class);
    $reservationService->reserveForOrder($order);

    $this->stockItem->refresh();
    expect($this->stockItem->quantity_reserved)->toBe(10.0);

    $reservationService->releaseForOrder($order);

    $this->stockItem->refresh();
    expect($this->stockItem->quantity_reserved)->toBe(0.0);
});

it('checks stock availability correctly', function () {
    $reservationService = app(\App\Services\Inventory\StockReservationService::class);

    $available = $reservationService->checkAvailability(
        $this->product,
        $this->warehouse,
        \App\ValueObjects\Quantity::from(50)
    );

    expect($available)->toBeTrue();
});

it('reports unavailability when stock insufficient', function () {
    $reservationService = app(\App\Services\Inventory\StockReservationService::class);

    $available = $reservationService->checkAvailability(
        $this->product,
        $this->warehouse,
        \App\ValueObjects\Quantity::from(200)
    );

    expect($available)->toBeFalse();
});

it('reserves multiple products for an order', function () {
    $product2 = Product::factory()->create(['company_id' => $this->user->company_id]);
    StockItem::factory()->create([
        'company_id' => $this->user->company_id,
        'product_id' => $product2->id,
        'warehouse_id' => $this->warehouse->id,
        'quantity_on_hand' => 50,
    ]);

    $order = Order::factory()->create([
        'company_id' => $this->user->company_id,
        'warehouse_id' => $this->warehouse->id,
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $this->product->id,
        'quantity' => 10,
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product2->id,
        'quantity' => 5,
    ]);

    $reservationService = app(\App\Services\Inventory\StockReservationService::class);
    $reservationService->reserveForOrder($order);

    $this->stockItem->refresh();
    expect($this->stockItem->quantity_reserved)->toBe(10.0);

    $stockItem2 = StockItem::where('product_id', $product2->id)->first();
    expect($stockItem2->quantity_reserved)->toBe(5.0);
});
