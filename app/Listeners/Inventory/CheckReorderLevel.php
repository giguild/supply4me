<?php

namespace App\Listeners\Inventory;

use App\Events\Inventory\StockReserved;
use App\Events\Inventory\StockReleased;
use App\Enums\Inventory\StockStatus;
use App\Models\Inventory\StockItem;
use App\Events\Inventory\StockLow;
use App\Events\Inventory\StockOut;
use Illuminate\Contracts\Queue\ShouldQueue;

class CheckReorderLevel implements ShouldQueue
{
    public function handle(StockReserved|StockReleased $event): void
    {
        $stockItem = $event->stockItem->fresh();

        if (!$stockItem) {
            return;
        }

        $availableQuantity = $stockItem->quantity_on_hand - $stockItem->quantity_reserved;

        if ($availableQuantity <= 0) {
            $stockItem->update(['status' => StockStatus::OutOfStock]);
            event(new StockOut($stockItem));
        } elseif ($availableQuantity <= $stockItem->reorder_level) {
            $stockItem->update(['status' => StockStatus::LowStock]);
            event(new StockLow($stockItem, $stockItem->reorder_level));
        } elseif ($availableQuantity > $stockItem->reorder_level) {
            $stockItem->update(['status' => StockStatus::InStock]);
        }
    }
}
