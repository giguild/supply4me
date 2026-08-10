<?php

use App\Models\Core\User;
use App\Models\Inventory\StockTransfer;
use App\Models\Inventory\Warehouse;
use App\Models\Products\Product;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
    $this->fromWarehouse = Warehouse::factory()->create(['company_id' => $this->user->company_id]);
    $this->toWarehouse = Warehouse::factory()->create(['company_id' => $this->user->company_id]);
    $this->product = Product::factory()->create(['company_id' => $this->user->company_id]);
});

it('can create a stock transfer', function () {
    $response = $this->postJson('/api/v1/stock-transfers', [
        'from_warehouse_id' => $this->fromWarehouse->id,
        'to_warehouse_id' => $this->toWarehouse->id,
        'notes' => 'Transfer to main warehouse',
        'expected_date' => now()->addDays(5)->toDateString(),
        'items' => [
            [
                'product_id' => $this->product->id,
                'quantity' => 25,
                'notes' => 'Urgent transfer',
            ],
        ],
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'success',
            'message',
            'data',
        ]);

    $this->assertDatabaseHas('stock_transfers', [
        'from_warehouse_id' => $this->fromWarehouse->id,
        'to_warehouse_id' => $this->toWarehouse->id,
        'status' => 'pending',
    ]);
});

it('validates from and to warehouse are different', function () {
    $response = $this->postJson('/api/v1/stock-transfers', [
        'from_warehouse_id' => $this->fromWarehouse->id,
        'to_warehouse_id' => $this->fromWarehouse->id,
        'items' => [
            [
                'product_id' => $this->product->id,
                'quantity' => 10,
            ],
        ],
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['to_warehouse_id']);
});

it('validates required fields for stock transfer', function () {
    $response = $this->postJson('/api/v1/stock-transfers', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors([
            'from_warehouse_id',
            'to_warehouse_id',
            'items',
        ]);
});

it('can approve a pending transfer', function () {
    $transfer = StockTransfer::factory()->create([
        'company_id' => $this->user->company_id,
        'from_warehouse_id' => $this->fromWarehouse->id,
        'to_warehouse_id' => $this->toWarehouse->id,
        'status' => 'pending',
    ]);

    $response = $this->postJson("/api/v1/stock-transfers/{$transfer->id}/approve");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Transfer approved successfully',
        ]);

    $this->assertDatabaseHas('stock_transfers', [
        'id' => $transfer->id,
        'status' => 'approved',
        'approved_by' => $this->user->id,
    ]);
});

it('can ship an approved transfer', function () {
    $transfer = StockTransfer::factory()->create([
        'company_id' => $this->user->company_id,
        'from_warehouse_id' => $this->fromWarehouse->id,
        'to_warehouse_id' => $this->toWarehouse->id,
        'status' => 'approved',
    ]);

    $response = $this->postJson("/api/v1/stock-transfers/{$transfer->id}/ship");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Transfer shipped successfully',
        ]);

    $this->assertDatabaseHas('stock_transfers', [
        'id' => $transfer->id,
        'status' => 'shipped',
    ]);
});

it('can receive a shipped transfer', function () {
    $transfer = StockTransfer::factory()->create([
        'company_id' => $this->user->company_id,
        'from_warehouse_id' => $this->fromWarehouse->id,
        'to_warehouse_id' => $this->toWarehouse->id,
        'status' => 'shipped',
    ]);

    $response = $this->postJson("/api/v1/stock-transfers/{$transfer->id}/receive", [
        'items' => [
            [
                'stock_transfer_item_id' => 1,
                'received_quantity' => 25,
                'condition' => 'good',
            ],
        ],
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Transfer received successfully',
        ]);

    $this->assertDatabaseHas('stock_transfers', [
        'id' => $transfer->id,
        'status' => 'received',
        'received_by' => $this->user->id,
    ]);
});

it('cannot approve a shipped transfer', function () {
    $transfer = StockTransfer::factory()->create([
        'company_id' => $this->user->company_id,
        'from_warehouse_id' => $this->fromWarehouse->id,
        'to_warehouse_id' => $this->toWarehouse->id,
        'status' => 'shipped',
    ]);

    $response = $this->postJson("/api/v1/stock-transfers/{$transfer->id}/approve");

    $response->assertStatus(422);
});

it('cannot ship a pending transfer', function () {
    $transfer = StockTransfer::factory()->create([
        'company_id' => $this->user->company_id,
        'from_warehouse_id' => $this->fromWarehouse->id,
        'to_warehouse_id' => $this->toWarehouse->id,
        'status' => 'pending',
    ]);

    $response = $this->postJson("/api/v1/stock-transfers/{$transfer->id}/ship");

    $response->assertStatus(422);
});

it('cannot receive a pending transfer', function () {
    $transfer = StockTransfer::factory()->create([
        'company_id' => $this->user->company_id,
        'from_warehouse_id' => $this->fromWarehouse->id,
        'to_warehouse_id' => $this->toWarehouse->id,
        'status' => 'pending',
    ]);

    $response = $this->postJson("/api/v1/stock-transfers/{$transfer->id}/receive", [
        'items' => [],
    ]);

    $response->assertStatus(422);
});

it('can show a stock transfer', function () {
    $transfer = StockTransfer::factory()->create([
        'company_id' => $this->user->company_id,
        'from_warehouse_id' => $this->fromWarehouse->id,
        'to_warehouse_id' => $this->toWarehouse->id,
    ]);

    $response = $this->getJson("/api/v1/stock-transfers/{$transfer->id}");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data',
        ]);
});

it('can list stock transfers', function () {
    StockTransfer::factory()->count(3)->create([
        'company_id' => $this->user->company_id,
        'from_warehouse_id' => $this->fromWarehouse->id,
        'to_warehouse_id' => $this->toWarehouse->id,
    ]);

    $response = $this->getJson('/api/v1/stock-transfers');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data',
            'meta',
        ]);
});
