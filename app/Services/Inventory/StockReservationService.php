<?php

namespace App\Services\Inventory;

use App\Enums\Inventory\MovementType;
use App\Models\Inventory\StockItem;
use App\Models\Orders\Order;
use App\Models\Products\Product;
use App\Models\Inventory\Warehouse;
use App\ValueObjects\Quantity;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StockReservationService
{
    public function __construct(
        private readonly StockMovementService $stockMovementService,
    ) {}

    /**
     * Reserve stock for all items in an order.
     */
    public function reserveForOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                $product = $item->product;

                if (!$product || !$product->is_stockable) {
                    continue;
                }

                $stockItem = StockItem::where('product_id', $product->id)
                    ->where('warehouse_id', $order->warehouse_id)
                    ->first();

                if (!$stockItem) {
                    throw new \RuntimeException(
                        "No stock found for product {$product->name} in warehouse {$order->warehouse_id}"
                    );
                }

                $quantity = Quantity::from((float) $item->quantity);

                if (!$this->reserveStock($stockItem, $quantity)) {
                    throw new \RuntimeException(
                        "Insufficient stock for product {$product->name}. "
                        . "Requested: {$quantity->getValue()}, "
                        . "Available: " . $this->getAvailableQuantity($stockItem)
                    );
                }
            }
        });
    }

    /**
     * Release reserved stock for all items in an order.
     */
    public function releaseForOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                $product = $item->product;

                if (!$product || !$product->is_stockable) {
                    continue;
                }

                $stockItem = StockItem::where('product_id', $product->id)
                    ->where('warehouse_id', $order->warehouse_id)
                    ->first();

                if (!$stockItem) {
                    continue;
                }

                $quantity = Quantity::from((float) $item->quantity);
                $this->releaseStock($stockItem, $quantity);
            }
        });
    }

    /**
     * Reserve stock for a specific stock item using optimistic locking.
     */
    public function reserveStock(StockItem $stockItem, Quantity $quantity): bool
    {
        $maxRetries = 3;

        for ($attempt = 0; $attempt < $maxRetries; $attempt++) {
            $availableQuantity = $this->getAvailableQuantity($stockItem);

            if ($availableQuantity < $quantity->getValue()) {
                return false;
            }

            $originalVersion = $stockItem->version;

            $updated = DB::table('stock_items')
                ->where('id', $stockItem->id)
                ->where('version', $originalVersion)
                ->update([
                    'quantity_reserved' => DB::raw('quantity_reserved + ' . $quantity->getValue()),
                    'version' => DB::raw('version + 1'),
                    'updated_at' => now(),
                ]);

            if ($updated) {
                $stockItem->refresh();

                $this->stockMovementService->recordAdjustment(
                    $stockItem,
                    $quantity,
                    'Reservation for order'
                );

                return true;
            }

            $stockItem->refresh();
        }

        return false;
    }

    /**
     * Release previously reserved stock.
     */
    private function releaseStock(StockItem $stockItem, Quantity $quantity): void
    {
        $maxRetries = 3;

        for ($attempt = 0; $attempt < $maxRetries; $attempt++) {
            $originalVersion = $stockItem->version;

            $updated = DB::table('stock_items')
                ->where('id', $stockItem->id)
                ->where('version', $originalVersion)
                ->where('quantity_reserved', '>=', $quantity->getValue())
                ->update([
                    'quantity_reserved' => DB::raw('quantity_reserved - ' . $quantity->getValue()),
                    'version' => DB::raw('version + 1'),
                    'updated_at' => now(),
                ]);

            if ($updated) {
                $stockItem->refresh();

                $this->stockMovementService->recordAdjustment(
                    $stockItem,
                    Quantity::from(-$quantity->getValue()),
                    'Release reservation'
                );

                return;
            }

            $stockItem->refresh();
        }
    }

    /**
     * Check if sufficient stock is available in a warehouse for a product.
     */
    public function checkAvailability(Product $product, Warehouse $warehouse, Quantity $quantity): bool
    {
        $stockItem = StockItem::where('product_id', $product->id)
            ->where('warehouse_id', $warehouse->id)
            ->first();

        if (!$stockItem) {
            return false;
        }

        return $this->getAvailableQuantity($stockItem) >= $quantity->getValue();
    }

    /**
     * Get available quantity for a stock item (on hand minus reserved).
     */
    private function getAvailableQuantity(StockItem $stockItem): float
    {
        return max(0, (float) $stockItem->quantity_on_hand - (float) $stockItem->quantity_reserved);
    }
}
