<?php

use App\Models\Core\User;
use App\Models\Inventory\StockAdjustment;
use App\Models\Inventory\StockAdjustmentItem;
use App\Models\Inventory\Warehouse;
use App\Models\Products\Product;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
    $this->warehouse = Warehouse::factory()->create(['company_id' => $this->user->company_id]);
    $this->product = Product::factory()->create(['company_id' => $this->user->company_id]);
});

it('can create a stock adjustment', function () {
    $response = $this->postJson('/api/v1/stock-adjustments', [
        'warehouse_id' => $this->warehouse->id,
        'type' => 'increase',
        'reason' => 'Inventory count correction',
        'items' => [
            [
                'product_id' => $this->product->id,
                'quantity' => 50,
                'unit_cost' => 10,
                'notes' => 'Found additional stock',
            ],
        ],
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'success',
            'message',
            'data',
        ]);

    $this->assertDatabaseHas('stock_adjustments', [
        'warehouse_id' => $this->warehouse->id,
        'type' => 'increase',
        'status' => 'pending',
    ]);
});

it('validates required fields for stock adjustment', function () {
    $response = $this->postJson('/api/v1/stock-adjustments', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors([
            'warehouse_id',
            'type',
            'reason',
            'items',
        ]);
});

it('validates adjustment type is valid', function () {
    $response = $this->postJson('/api/v1/stock-adjustments', [
        'warehouse_id' => $this->warehouse->id,
        'type' => 'invalid_type',
        'reason' => 'Test',
        'items' => [],
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['type']);
});

it('validates items array is not empty', function () {
    $response = $this->postJson('/api/v1/stock-adjustments', [
        'warehouse_id' => $this->warehouse->id,
        'type' => 'increase',
        'reason' => 'Test',
        'items' => [],
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['items']);
});

it('can approve a pending adjustment', function () {
    $adjustment = StockAdjustment::factory()->create([
        'company_id' => $this->user->company_id,
        'warehouse_id' => $this->warehouse->id,
        'status' => 'pending',
    ]);

    $response = $this->postJson("/api/v1/stock-adjustments/{$adjustment->id}/approve");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Adjustment approved successfully',
        ]);

    $this->assertDatabaseHas('stock_adjustments', [
        'id' => $adjustment->id,
        'status' => 'approved',
        'approved_by' => $this->user->id,
    ]);
});

it('can reject a pending adjustment', function () {
    $adjustment = StockAdjustment::factory()->create([
        'company_id' => $this->user->company_id,
        'warehouse_id' => $this->warehouse->id,
        'status' => 'pending',
    ]);

    $response = $this->postJson("/api/v1/stock-adjustments/{$adjustment->id}/reject", [
        'reason' => 'Incorrect quantities',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Adjustment rejected successfully',
        ]);

    $this->assertDatabaseHas('stock_adjustments', [
        'id' => $adjustment->id,
        'status' => 'rejected',
    ]);
});

it('cannot approve an already approved adjustment', function () {
    $adjustment = StockAdjustment::factory()->create([
        'company_id' => $this->user->company_id,
        'warehouse_id' => $this->warehouse->id,
        'status' => 'approved',
    ]);

    $response = $this->postJson("/api/v1/stock-adjustments/{$adjustment->id}/approve");

    $response->assertStatus(422);
});

it('can show a stock adjustment', function () {
    $adjustment = StockAdjustment::factory()->create([
        'company_id' => $this->user->company_id,
        'warehouse_id' => $this->warehouse->id,
    ]);

    $response = $this->getJson("/api/v1/stock-adjustments/{$adjustment->id}");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data',
        ]);
});

it('can list stock adjustments', function () {
    StockAdjustment::factory()->count(3)->create([
        'company_id' => $this->user->company_id,
        'warehouse_id' => $this->warehouse->id,
    ]);

    $response = $this->getJson('/api/v1/stock-adjustments');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data',
            'meta',
        ]);
});
