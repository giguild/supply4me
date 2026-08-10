<?php

namespace App\Listeners\Inventory;

use App\Events\Inventory\StockAdjusted;
use App\Events\Inventory\StockReleased;
use App\Events\Inventory\StockReserved;
use App\Events\Inventory\StockTransferred;
use App\Models\Inventory\StockMovement;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogStockMovement implements ShouldQueue
{
    public function handle(StockReserved|StockReleased|StockAdjusted|StockTransferred $event): void
    {
        $eventName = class_basename($event);

        $movementType = match ($eventName) {
            'StockReserved' => 'reservation',
            'StockReleased' => 'release',
            'StockAdjusted' => 'adjustment',
            'StockTransferred' => 'transfer',
            default => 'unknown',
        };

        StockMovement::create([
            'company_id' => $event->stockItem->company_id ?? $event->stockAdjustment?->company_id ?? $event->stockTransfer?->company_id,
            'stock_item_id' => $event->stockItem->id ?? null,
            'product_id' => $event->stockItem->product_id ?? null,
            'warehouse_id' => $event->stockItem->warehouse_id ?? null,
            'type' => $movementType,
            'quantity' => $event->quantity ?? 0,
            'reference_type' => get_class($event),
            'reference_id' => $event->stockItem->id ?? $event->stockAdjustment->id ?? $event->stockTransfer->id,
            'performed_by' => $event->user?->id ?? null,
            'notes' => "Stock {$movementType} triggered by {$eventName}",
        ]);
    }
}
