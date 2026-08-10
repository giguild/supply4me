<?php

namespace App\Services\Inventory;

use App\Models\Inventory\StockItem;
use App\Models\Inventory\Warehouse;
use Illuminate\Support\Collection;

class ReorderService
{
    /**
     * Check reorder levels for all stock items in a warehouse.
     */
    public function checkReorderLevels(Warehouse $warehouse): Collection
    {
        return StockItem::where('warehouse_id', $warehouse->id)
            ->where('status', 'active')
            ->whereColumn('quantity_on_hand', '<=', 'reorder_level')
            ->with('product', 'warehouse')
            ->get();
    }

    /**
     * Get reorder suggestions across all warehouses.
     */
    public function getReorderSuggestions(): Collection
    {
        return StockItem::where('status', 'active')
            ->whereColumn('quantity_on_hand', '<=', 'reorder_level')
            ->with('product', 'warehouse')
            ->get()
            ->map(function (StockItem $stockItem) {
                $reorderQuantity = max(
                    (float) $stockItem->reorder_quantity,
                    (float) $stockItem->reorder_level - (float) $stockItem->quantity_on_hand + (float) $stockItem->quantity_on_order
                );

                return [
                    'stock_item_id' => $stockItem->id,
                    'product' => $stockItem->product,
                    'warehouse' => $stockItem->warehouse,
                    'current_quantity' => (float) $stockItem->quantity_on_hand,
                    'reorder_level' => (float) $stockItem->reorder_level,
                    'quantity_on_order' => (float) $stockItem->quantity_on_order,
                    'suggested_reorder_quantity' => $reorderQuantity,
                    'urgency' => $this->calculateUrgency($stockItem),
                ];
            });
    }

    /**
     * Create a reorder alert for a stock item.
     */
    public function createReorderAlert(StockItem $stockItem): void
    {
        $available = (float) $stockItem->quantity_on_hand - (float) $stockItem->quantity_reserved;

        if ($available > (float) $stockItem->reorder_level) {
            return;
        }

        event(new \App\Events\Inventory\ReorderAlertCreated($stockItem));
    }

    /**
     * Calculate urgency level for a reorder suggestion.
     */
    private function calculateUrgency(StockItem $stockItem): string
    {
        $available = (float) $stockItem->quantity_on_hand - (float) $stockItem->quantity_reserved;
        $reorderLevel = (float) $stockItem->reorder_level;

        if ($available <= 0) {
            return 'critical';
        }

        if ($available <= $reorderLevel * 0.25) {
            return 'high';
        }

        if ($available <= $reorderLevel * 0.5) {
            return 'medium';
        }

        return 'low';
    }
}
