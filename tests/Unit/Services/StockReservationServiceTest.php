<?php

use App\Models\Core\User;
use App\Models\Inventory\StockItem;
use App\Models\Inventory\Warehouse;
use App\Models\Orders\Order;
use App\Models\Orders\OrderItem;
use App\Models\Products\Product;
use App\Services\Inventory\StockReservationService;
use App\ValueObjects\Quantity;

it('reserves stock for all order items', function () {
    $user = User::factory()->create();
    $warehouse = Warehouse::factory()->create(['company_id' => $user->company_id]);
    $product = Product::factory()->create(['company_id' => $user->company_id]);

    $stockItem = StockItem::factory()->create([
        'company_id' => $user->company_id,
        'product_id' => $product->id,
        'warehouse_id' => $warehouse->id,
        'quantity_on_hand' => 100,
    ]);

    $order = Order::factory()->create([
        'company_id' => $user->company_id,
        'warehouse_id' => $warehouse->id,
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => 15,
    ]);

    $service = app(StockReservationService::class);
    $service->reserveForOrder($order);

    $stockItem->refresh();
    expect($stockItem->quantity_reserved)->toBe(15.0);
});

it('releases stock for all order items', function () {
    $user = User::factory()->create();
    $warehouse = Warehouse::factory()->create(['company_id' => $user->company_id]);
    $product = Product::factory()->create(['company_id' => $user->company_id]);

    $stockItem = StockItem::factory()->create([
        'company_id' => $user->company_id,
        'product_id' => $product->id,
        'warehouse_id' => $warehouse->id,
        'quantity_on_hand' => 100,
        'quantity_reserved' => 20,
    ]);

    $order = Order::factory()->create([
        'company_id' => $user->company_id,
        'warehouse_id' => $warehouse->id,
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => 20,
    ]);

    $service = app(StockReservationService::class);
    $service->releaseForOrder($order);

    $stockItem->refresh();
    expect($stockItem->quantity_reserved)->toBe(0.0);
});

it('checks availability correctly', function () {
    $user = User::factory()->create();
    $warehouse = Warehouse::factory()->create(['company_id' => $user->company_id]);
    $product = Product::factory()->create(['company_id' => $user->company_id]);

    StockItem::factory()->create([
        'company_id' => $user->company_id,
        'product_id' => $product->id,
        'warehouse_id' => $warehouse->id,
        'quantity_on_hand' => 50,
        'quantity_reserved' => 10,
    ]);

    $service = app(StockReservationService::class);

    $available = $service->checkAvailability(
        $product,
        $warehouse,
        Quantity::from(40)
    );

    expect($available)->toBeTrue();
});

it('reports unavailability when stock insufficient', function () {
    $user = User::factory()->create();
    $warehouse = Warehouse::factory()->create(['company_id' => $user->company_id]);
    $product = Product::factory()->create(['company_id' => $user->company_id]);

    StockItem::factory()->create([
        'company_id' => $user->company_id,
        'product_id' => $product->id,
        'warehouse_id' => $warehouse->id,
        'quantity_on_hand' => 5,
        'quantity_reserved' => 0,
    ]);

    $service = app(StockReservationService::class);

    $available = $service->checkAvailability(
        $product,
        $warehouse,
        Quantity::from(10)
    );

    expect($available)->toBeFalse();
});

it('reserves stock with optimistic locking', function () {
    $user = User::factory()->create();
    $warehouse = Warehouse::factory()->create(['company_id' => $user->company_id]);
    $product = Product::factory()->create(['company_id' => $user->company_id]);

    $stockItem = StockItem::factory()->create([
        'company_id' => $user->company_id,
        'product_id' => $product->id,
        'warehouse_id' => $warehouse->id,
        'quantity_on_hand' => 100,
        'quantity_reserved' => 0,
        'version' => 1,
    ]);

    $service = app(StockReservationService::class);
    $result = $service->reserveStock($stockItem, Quantity::from(10));

    expect($result)->toBeTrue();

    $stockItem->refresh();
    expect($stockItem->quantity_reserved)->toBe(10.0)
        ->and($stockItem->version)->toBe(2);
});
