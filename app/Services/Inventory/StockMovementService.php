<?php

namespace App\Services\Inventory;

use App\Enums\Inventory\MovementType;
use App\Models\Inventory\StockItem;
use App\Models\Inventory\StockMovement;
use App\Models\Inventory\Warehouse;
use App\ValueObjects\Money;
use App\ValueObjects\Quantity;

class StockMovementService
{
    /**
     * Record a stock receipt (goods received into inventory).
     */
    public function recordReceipt(StockItem $stockItem, Quantity $quantity, Money $cost): StockMovement
    {
        $quantityBefore = (float) $stockItem->quantity_on_hand;
        $quantityAfter = $quantityBefore + $quantity->getValue();

        $stockItem->update([
            'quantity_on_hand' => $quantityAfter,
            'last_received_at' => now(),
        ]);

        $unitCost = $cost->getAmount() / $quantity->getValue();

        return StockMovement::create([
            'company_id' => $stockItem->company_id,
            'stock_item_id' => $stockItem->id,
            'movement_type' => MovementType::Receipt,
            'quantity' => $quantity->getValue(),
            'quantity_before' => $quantityBefore,
            'quantity_after' => $quantityAfter,
            'unit_cost' => $unitCost,
            'total_cost' => $cost->getAmount(),
            'performed_by' => auth()->id(),
        ]);
    }

    /**
     * Record a stock sale (goods sold from inventory).
     */
    public function recordSale(StockItem $stockItem, Quantity $quantity): StockMovement
    {
        $quantityBefore = (float) $stockItem->quantity_on_hand;
        $quantityAfter = $quantityBefore - $quantity->getValue();

        if ($quantityAfter < 0) {
            throw new \RuntimeException(
                "Insufficient stock. Available: {$quantityBefore}, Requested: {$quantity->getValue()}"
            );
        }

        $stockItem->update([
            'quantity_on_hand' => $quantityAfter,
            'last_sold_at' => now(),
        ]);

        $unitCost = (float) $stockItem->cost_price;

        return StockMovement::create([
            'company_id' => $stockItem->company_id,
            'stock_item_id' => $stockItem->id,
            'movement_type' => MovementType::Sale,
            'quantity' => -$quantity->getValue(),
            'quantity_before' => $quantityBefore,
            'quantity_after' => $quantityAfter,
            'unit_cost' => $unitCost,
            'total_cost' => $unitCost * $quantity->getValue(),
            'performed_by' => auth()->id(),
        ]);
    }

    /**
     * Record a stock transfer to another warehouse.
     */
    public function recordTransfer(StockItem $stockItem, Quantity $quantity, Warehouse $toWarehouse): StockMovement
    {
        $quantityBefore = (float) $stockItem->quantity_on_hand;
        $quantityAfter = $quantityBefore - $quantity->getValue();

        if ($quantityAfter < 0) {
            throw new \RuntimeException(
                "Insufficient stock for transfer. Available: {$quantityBefore}, Requested: {$quantity->getValue()}"
            );
        }

        $stockItem->update([
            'quantity_on_hand' => $quantityAfter,
        ]);

        $unitCost = (float) $stockItem->cost_price;

        return StockMovement::create([
            'company_id' => $stockItem->company_id,
            'stock_item_id' => $stockItem->id,
            'movement_type' => MovementType::Transfer,
            'quantity' => -$quantity->getValue(),
            'quantity_before' => $quantityBefore,
            'quantity_after' => $quantityAfter,
            'from_warehouse_id' => $stockItem->warehouse_id,
            'to_warehouse_id' => $toWarehouse->id,
            'unit_cost' => $unitCost,
            'total_cost' => $unitCost * $quantity->getValue(),
            'performed_by' => auth()->id(),
        ]);
    }

    /**
     * Record a stock adjustment (audit, damage, correction).
     */
    public function recordAdjustment(StockItem $stockItem, Quantity $quantity, string $reason): StockMovement
    {
        $quantityBefore = (float) $stockItem->quantity_on_hand;
        $quantityAfter = $quantityBefore + $quantity->getValue();

        if ($quantityAfter < 0) {
            throw new \RuntimeException(
                "Adjustment would result in negative stock. Available: {$quantityBefore}, Adjustment: {$quantity->getValue()}"
            );
        }

        $stockItem->update([
            'quantity_on_hand' => $quantityAfter,
        ]);

        $unitCost = (float) $stockItem->cost_price;

        return StockMovement::create([
            'company_id' => $stockItem->company_id,
            'stock_item_id' => $stockItem->id,
            'movement_type' => MovementType::Adjustment,
            'quantity' => $quantity->getValue(),
            'quantity_before' => $quantityBefore,
            'quantity_after' => $quantityAfter,
            'unit_cost' => $unitCost,
            'total_cost' => $unitCost * abs($quantity->getValue()),
            'reason' => $reason,
            'performed_by' => auth()->id(),
        ]);
    }
}
