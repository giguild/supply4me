<?php

namespace App\Actions\Reports;

use App\Models\Inventory\StockItem;
use App\Models\Products\Product;

class GenerateInventoryReportAction
{
    public function execute(array $data): array
    {
        $companyId = $data['company_id'];
        $warehouseId = $data['warehouse_id'] ?? null;

        $query = StockItem::where('company_id', $companyId)
            ->with('product', 'warehouse');

        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }

        $stockItems = $query->get();

        $totalProducts = $stockItems->count('product_id')->unique()->count();
        $totalStockValue = $stockItems->sum(fn ($item) => $item->quantity_on_hand * $item->cost_price);
        $totalQuantityOnHand = $stockItems->sum('quantity_on_hand');
        $totalQuantityReserved = $stockItems->sum('quantity_reserved');

        $lowStockItems = $stockItems->filter(function ($item) {
            return $item->quantity_on_hand <= $item->reorder_level && $item->reorder_level > 0;
        })->map(fn ($item) => [
            'product_id' => $item->product_id,
            'product_name' => $item->product?->name,
            'sku' => $item->product?->sku,
            'warehouse' => $item->warehouse?->name,
            'quantity_on_hand' => $item->quantity_on_hand,
            'reorder_level' => $item->reorder_level,
        ])->values()->toArray();

        $outOfStockItems = $stockItems->filter(fn ($item) => $item->quantity_on_hand <= 0)
            ->map(fn ($item) => [
                'product_id' => $item->product_id,
                'product_name' => $item->product?->name,
                'sku' => $item->product?->sku,
                'warehouse' => $item->warehouse?->name,
            ])->values()->toArray();

        $stockByWarehouse = $stockItems->groupBy('warehouse_id')
            ->map(fn ($group, $warehouseId) => [
                'warehouse_id' => $warehouseId,
                'warehouse_name' => $group->first()->warehouse?->name,
                'total_quantity' => $group->sum('quantity_on_hand'),
                'total_value' => $group->sum(fn ($item) => $item->quantity_on_hand * $item->cost_price),
                'product_count' => $group->count('product_id')->unique()->count(),
            ])->values()->toArray();

        return [
            'summary' => [
                'total_products' => $totalProducts,
                'total_stock_value' => round($totalStockValue, 2),
                'total_quantity_on_hand' => $totalQuantityOnHand,
                'total_quantity_reserved' => $totalQuantityReserved,
                'available_quantity' => $totalQuantityOnHand - $totalQuantityReserved,
            ],
            'low_stock_items' => $lowStockItems,
            'out_of_stock_items' => $outOfStockItems,
            'stock_by_warehouse' => $stockByWarehouse,
        ];
    }
}
