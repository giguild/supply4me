<?php

namespace App\Services\Reporting;

use App\Models\Inventory\StockItem;
use App\Models\Inventory\StockMovement;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class InventoryReportService
{
    /**
     * Get current stock levels for a warehouse.
     */
    public function getStockLevels(string $warehouseId): Collection
    {
        return StockItem::where('warehouse_id', $warehouseId)
            ->where('status', 'active')
            ->with('product', 'warehouse')
            ->get()
            ->map(function (StockItem $item) {
                return [
                    'product_id' => $item->product_id,
                    'product_name' => $item->product?->name,
                    'sku' => $item->product?->sku,
                    'warehouse' => $item->warehouse?->name,
                    'quantity_on_hand' => (float) $item->quantity_on_hand,
                    'quantity_reserved' => (float) $item->quantity_reserved,
                    'quantity_available' => (float) $item->quantity_on_hand - (float) $item->quantity_reserved,
                    'quantity_on_order' => (float) $item->quantity_on_order,
                    'reorder_level' => (float) $item->reorder_level,
                    'cost_price' => (float) $item->cost_price,
                    'total_value' => (float) $item->quantity_on_hand * (float) $item->cost_price,
                    'status' => $this->getStockStatus($item),
                ];
            });
    }

    /**
     * Get stock movements for a warehouse within a date range.
     */
    public function getStockMovements(string $warehouseId, string $startDate, string $endDate): Collection
    {
        return StockMovement::whereHas('stockItem', function ($query) use ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with('stockItem.product')
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Get stock valuation for a warehouse.
     */
    public function getStockValuation(string $warehouseId): array
    {
        $stockItems = StockItem::where('warehouse_id', $warehouseId)
            ->where('status', 'active')
            ->get();

        $totalValue = $stockItems->sum(function (StockItem $item) {
            return (float) $item->quantity_on_hand * (float) $item->cost_price;
        });

        $totalQuantity = $stockItems->sum('quantity_on_hand');

        return [
            'warehouse_id' => $warehouseId,
            'total_items' => $stockItems->count(),
            'total_quantity' => $totalQuantity,
            'total_value' => $totalValue,
            'average_cost' => $totalQuantity > 0 ? $totalValue / $totalQuantity : 0,
            'items' => $stockItems->map(function (StockItem $item) {
                return [
                    'product_id' => $item->product_id,
                    'product_name' => $item->product?->name,
                    'sku' => $item->product?->sku,
                    'quantity' => (float) $item->quantity_on_hand,
                    'cost_price' => (float) $item->cost_price,
                    'total_value' => (float) $item->quantity_on_hand * (float) $item->cost_price,
                ];
            }),
        ];
    }

    /**
     * Get inventory aging report.
     */
    public function getAgingReport(string $warehouseId): Collection
    {
        return StockItem::where('warehouse_id', $warehouseId)
            ->where('status', 'active')
            ->where('quantity_on_hand', '>', 0)
            ->with('product')
            ->get()
            ->map(function (StockItem $item) {
                $lastSoldAt = $item->last_sold_at;
                $daysSinceLastSale = $lastSoldAt
                    ? $lastSoldAt->diffInDays(now())
                    : null;

                return [
                    'product_id' => $item->product_id,
                    'product_name' => $item->product?->name,
                    'sku' => $item->product?->sku,
                    'quantity' => (float) $item->quantity_on_hand,
                    'last_sold_at' => $lastSoldAt?->toDateString(),
                    'days_since_last_sale' => $daysSinceLastSale,
                    'total_value' => (float) $item->quantity_on_hand * (float) $item->cost_price,
                    'aging_category' => $this->getAgingCategory($daysSinceLastSale),
                ];
            })
            ->sortByDesc('days_since_last_sale')
            ->values();
    }

    /**
     * Determine stock status based on levels.
     */
    private function getStockStatus(StockItem $item): string
    {
        $available = (float) $item->quantity_on_hand - (float) $item->quantity_reserved;

        if ($available <= 0) {
            return 'out_of_stock';
        }

        if ($available <= (float) $item->reorder_level) {
            return 'low_stock';
        }

        return 'in_stock';
    }

    /**
     * Categorize items based on days since last sale.
     */
    private function getAgingCategory(?int $days): string
    {
        if ($days === null) {
            return 'unknown';
        }

        return match (true) {
            $days <= 30 => '0-30 days',
            $days <= 60 => '31-60 days',
            $days <= 90 => '61-90 days',
            $days <= 180 => '91-180 days',
            default => '180+ days',
        };
    }
}
