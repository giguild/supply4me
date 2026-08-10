<?php

namespace App\Observers;

use App\Events\Inventory\StockLow;
use App\Events\Inventory\StockOut;
use App\Models\Inventory\StockItem;
use Spatie\Activitylog\Facades\ActivityLog;

class StockItemObserver
{
    public function created(StockItem $stockItem): void
    {
        ActivityLog::event('Stock item created')
            ->on($stockItem)
            ->withProperties([
                'stock_item_id' => $stockItem->id,
                'warehouse_id' => $stockItem->warehouse_id,
                'product_id' => $stockItem->product_id,
                'quantity_on_hand' => $stockItem->quantity_on_hand,
                'company_id' => $stockItem->company_id,
            ])
            ->log();

        $this->checkReorderLevels($stockItem);
    }

    public function updated(StockItem $stockItem): void
    {
        $changes = $stockItem->getChanges();

        ActivityLog::event('Stock item updated')
            ->on($stockItem)
            ->withProperties([
                'stock_item_id' => $stockItem->id,
                'warehouse_id' => $stockItem->warehouse_id,
                'product_id' => $stockItem->product_id,
                'attributes' => $changes,
                'old' => $stockItem->getOriginal(),
            ])
            ->log();

        if (isset($changes['quantity_on_hand']) || isset($changes['reorder_level'])) {
            $this->checkReorderLevels($stockItem);
        }
    }

    public function deleted(StockItem $stockItem): void
    {
        ActivityLog::event('Stock item deleted')
            ->on($stockItem)
            ->withProperties([
                'stock_item_id' => $stockItem->id,
                'warehouse_id' => $stockItem->warehouse_id,
                'product_id' => $stockItem->product_id,
                'quantity_on_hand' => $stockItem->quantity_on_hand,
            ])
            ->log();
    }

    public function restored(StockItem $stockItem): void
    {
        ActivityLog::event('Stock item restored')
            ->on($stockItem)
            ->withProperties([
                'stock_item_id' => $stockItem->id,
            ])
            ->log();

        $this->checkReorderLevels($stockItem);
    }

    protected function checkReorderLevels(StockItem $stockItem): void
    {
        $product = $stockItem->product;

        if ($product && $stockItem->quantity_on_hand <= 0) {
            StockOut::dispatch($stockItem, $product);
        } elseif ($product && $stockItem->reorder_level > 0 && $stockItem->quantity_on_hand <= $stockItem->reorder_level) {
            StockLow::dispatch($stockItem, $product);
        }
    }
}
