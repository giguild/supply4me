<?php

namespace App\Services\Inventory;

use App\Enums\Inventory\MovementType;
use App\Events\Inventory\StockLow;
use App\Events\Inventory\StockOut;
use App\Models\Delivery\Delivery;
use App\Models\Inventory\StockItem;
use App\Models\Inventory\StockMovement;
use App\Models\Orders\OrderItem;
use App\ValueObjects\Quantity;
use Illuminate\Support\Facades\DB;

class StockCommitService
{
    public function __construct(
        private readonly StockMovementService $stockMovementService,
    ) {}

    /**
     * Commit delivered quantities: decrement physical stock (quantity_on_hand)
     * and convert the matching reservation into a sale.
     */
    public function commitDelivery(Delivery $delivery): void
    {
        $order = $delivery->order;

        if (!$order || !$order->items->count()) {
            return;
        }

        $delivery->loadMissing('items');

        $usesDeliveryItems = $delivery->items->isNotEmpty();

        $deliveredByOrderItem = $delivery->items
            ->groupBy('order_item_id')
            ->map(fn ($items) => (float) $items->sum('quantity_delivered'));

        DB::transaction(function () use ($order, $delivery, $usesDeliveryItems, $deliveredByOrderItem) {
            foreach ($order->items as $item) {
                if (!$item->product_id) {
                    continue;
                }

                $quantity = $usesDeliveryItems
                    ? (float) ($deliveredByOrderItem->get($item->id) ?? 0)
                    : (float) $item->quantity;

                if ($quantity <= 0) {
                    continue;
                }

                $stockItem = StockItem::where('company_id', $order->company_id)
                    ->where('product_id', $item->product_id)
                    ->first();

                if (!$stockItem) {
                    continue;
                }

                $this->stockMovementService->recordSale($stockItem, Quantity::from($quantity));
                $this->releaseReserved($stockItem, $quantity, $delivery);

                $this->checkReorderLevel($stockItem, $item);
            }
        });
    }

    /**
     * Release the committed portion of a reservation without allowing the
     * reserved quantity to go negative.
     */
    private function releaseReserved(StockItem $stockItem, float $quantity, Delivery $delivery): void
    {
        $reserved = (float) $stockItem->quantity_reserved;

        if ($reserved <= 0) {
            return;
        }

        $toRelease = min($reserved, $quantity);

        if ($toRelease <= 0) {
            return;
        }

        $currentVersion = $stockItem->version;

        $updated = StockItem::where('id', $stockItem->id)
            ->where('version', $currentVersion)
            ->update([
                'quantity_reserved' => DB::raw('GREATEST(0, quantity_reserved - ' . $toRelease . ')'),
                'version' => DB::raw('version + 1'),
            ]);

        if (!$updated) {
            return;
        }

        $stockItem->refresh();

        $quantityBefore = (float) $stockItem->quantity_reserved + $toRelease;

        StockMovement::create([
            'company_id' => $stockItem->company_id,
            'stock_item_id' => $stockItem->id,
            'movement_type' => MovementType::Release,
            'quantity' => $toRelease,
            'quantity_before' => $quantityBefore,
            'quantity_after' => $stockItem->quantity_reserved,
            'reference_type' => Delivery::class,
            'reference_id' => $delivery->id,
            'reason' => "Released to fulfill delivery #{$delivery->delivery_number}",
            'performed_by' => $this->stockMovementService->actorId(),
        ]);
    }

    private function checkReorderLevel(StockItem $stockItem, OrderItem $item): void
    {
        $onHand = (float) $stockItem->quantity_on_hand;

        if ($onHand <= 0) {
            StockOut::dispatch($stockItem, $item->product);
        } elseif ((float) $stockItem->reorder_level > 0 && $onHand <= (float) $stockItem->reorder_level) {
            StockLow::dispatch($stockItem, $item->product);
        }
    }
}