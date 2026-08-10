<?php

namespace App\Actions\Inventory;

use App\Enums\Inventory\MovementType;
use App\Events\Inventory\StockReleased;
use App\Models\Inventory\StockItem;
use App\Models\Inventory\StockMovement;
use App\Models\Orders\Order;
use Illuminate\Support\Facades\DB;

class ReleaseStockAction
{
    public function execute(StockItem $stockItem, Order $order, float $quantity): StockItem
    {
        return DB::transaction(function () use ($stockItem, $order, $quantity) {
            if ($stockItem->quantity_reserved < $quantity) {
                throw new \App\Exceptions\InvalidStockReleaseException(
                    "Cannot release more than reserved. Reserved: {$stockItem->quantity_reserved}, Release: {$quantity}"
                );
            }

            $currentVersion = $stockItem->version;

            $updated = StockItem::where('id', $stockItem->id)
                ->where('version', $currentVersion)
                ->update([
                    'quantity_reserved' => DB::raw("quantity_reserved - {$quantity}"),
                    'version' => DB::raw('version + 1'),
                ]);

            if (! $updated) {
                throw new \App\Exceptions\StockOptimisticLockException(
                    'Stock item was modified by another process. Please retry.'
                );
            }

            $stockItem->refresh();

            $quantityBefore = $stockItem->quantity_reserved + $quantity;

            StockMovement::create([
                'company_id' => $stockItem->company_id,
                'stock_item_id' => $stockItem->id,
                'movement_type' => MovementType::Release,
                'quantity' => $quantity,
                'quantity_before' => $quantityBefore,
                'quantity_after' => $stockItem->quantity_reserved,
                'reference_type' => Order::class,
                'reference_id' => $order->id,
                'reason' => "Released for order #{$order->order_number}",
                'performed_by' => $order->assigned_to,
            ]);

            event(new StockReleased($stockItem, $order, $quantity));

            return $stockItem;
        });
    }
}
