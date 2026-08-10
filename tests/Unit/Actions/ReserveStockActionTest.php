<?php

use App\Actions\Inventory\ReserveStockAction;
use App\Models\Core\User;
use App\Models\Inventory\StockItem;
use App\Models\Inventory\Warehouse;
use App\Models\Orders\Order;
use App\Models\Products\Product;

it('reserves stock successfully', function () {
    $user = User::factory()->create();
    $warehouse = Warehouse::factory()->create(['company_id' => $user->company_id]);
    $product = Product::factory()->create(['company_id' => $user->company_id]);
    $order = Order::factory()->create(['company_id' => $user->company_id]);

    $stockItem = StockItem::factory()->create([
        'company_id' => $user->company_id,
        'product_id' => $product->id,
        'warehouse_id' => $warehouse->id,
        'quantity_on_hand' => 100,
        'quantity_reserved' => 0,
    ]);

    $action = app(ReserveStockAction::class);
    $result = $action->execute($stockItem, $order, 10);

    expect($result->quantity_reserved)->toBe(10.0)
        ->and($result->quantity_on_hand)->toBe(100.0);
});

it('throws exception when insufficient stock', function () {
    $user = User::factory()->create();
    $warehouse = Warehouse::factory()->create(['company_id' => $user->company_id]);
    $product = Product::factory()->create(['company_id' => $user->company_id]);
    $order = Order::factory()->create(['company_id' => $user->company_id]);

    $stockItem = StockItem::factory()->create([
        'company_id' => $user->company_id,
        'product_id' => $product->id,
        'warehouse_id' => $warehouse->id,
        'quantity_on_hand' => 5,
        'quantity_reserved' => 0,
    ]);

    $action = app(ReserveStockAction::class);

    $this->expectException(\App\Exceptions\InsufficientStockException::class);

    $action->execute($stockItem, $order, 10);
});

it('creates a stock movement record', function () {
    $user = User::factory()->create();
    $warehouse = Warehouse::factory()->create(['company_id' => $user->company_id]);
    $product = Product::factory()->create(['company_id' => $user->company_id]);
    $order = Order::factory()->create(['company_id' => $user->company_id]);

    $stockItem = StockItem::factory()->create([
        'company_id' => $user->company_id,
        'product_id' => $product->id,
        'warehouse_id' => $warehouse->id,
        'quantity_on_hand' => 50,
        'quantity_reserved' => 0,
    ]);

    $action = app(ReserveStockAction::class);
    $action->execute($stockItem, $order, 10);

    $this->assertDatabaseHas('stock_movements', [
        'stock_item_id' => $stockItem->id,
        'movement_type' => 'reservation',
        'quantity' => 10,
    ]);
});

it('uses optimistic locking for concurrent access', function () {
    $user = User::factory()->create();
    $warehouse = Warehouse::factory()->create(['company_id' => $user->company_id]);
    $product = Product::factory()->create(['company_id' => $user->company_id]);
    $order = Order::factory()->create(['company_id' => $user->company_id]);

    $stockItem = StockItem::factory()->create([
        'company_id' => $user->company_id,
        'product_id' => $product->id,
        'warehouse_id' => $warehouse->id,
        'quantity_on_hand' => 100,
        'quantity_reserved' => 0,
        'version' => 5,
    ]);

    $action = app(ReserveStockAction::class);
    $action->execute($stockItem, $order, 10);

    $stockItem->refresh();
    expect($stockItem->version)->toBe(6);
});

it('fails on version conflict', function () {
    $user = User::factory()->create();
    $warehouse = Warehouse::factory()->create(['company_id' => $user->company_id]);
    $product = Product::factory()->create(['company_id' => $user->company_id]);
    $order = Order::factory()->create(['company_id' => $user->company_id]);

    $stockItem = StockItem::factory()->create([
        'company_id' => $user->company_id,
        'product_id' => $product->id,
        'warehouse_id' => $warehouse->id,
        'quantity_on_hand' => 100,
        'quantity_reserved' => 0,
        'version' => 1,
    ]);

    StockItem::where('id', $stockItem->id)->update(['version' => 2]);

    $action = app(ReserveStockAction::class);

    $this->expectException(\App\Exceptions\StockOptimisticLockException::class);

    $action->execute($stockItem, $order, 10);
});
