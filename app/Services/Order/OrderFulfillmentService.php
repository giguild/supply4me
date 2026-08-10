<?php

namespace App\Services\Order;

use App\Enums\Orders\FulfillmentStatus;
use App\Models\Orders\Order;
use App\Models\Orders\OrderItem;
use Illuminate\Support\Collection;

class OrderFulfillmentService
{
    /**
     * Determine if an order can be fully fulfilled.
     */
    public function canFulfill(Order $order): bool
    {
        return $this->getUnfulfilledItems($order)->isEmpty();
    }

    /**
     * Get all items in an order that are not yet fulfilled.
     */
    public function getUnfulfilledItems(Order $order): Collection
    {
        return $order->items->filter(function (OrderItem $item) {
            return $this->isItemUnfulfilled($item);
        });
    }

    /**
     * Update the fulfillment status of an order based on its items.
     */
    public function updateFulfillmentStatus(Order $order): void
    {
        $totalItems = $order->items->count();
        $fulfilledItems = $order->items->filter(function (OrderItem $item) {
            return !$this->isItemUnfulfilled($item);
        })->count();

        if ($fulfilledItems === 0) {
            $status = FulfillmentStatus::Unfulfilled;
        } elseif ($fulfilledItems === $totalItems) {
            $status = FulfillmentStatus::Fulfilled;
        } else {
            $status = FulfillmentStatus::Partial;
        }

        $order->update(['fulfillment_status' => $status]);
    }

    /**
     * Mark an order as complete.
     */
    public function markComplete(Order $order): void
    {
        $unfulfilledItems = $this->getUnfulfilledItems($order);

        if ($unfulfilledItems->isNotEmpty()) {
            throw new \RuntimeException(
                'Cannot mark order as complete. There are still ' . $unfulfilledItems->count() . ' unfulfilled items.'
            );
        }

        $order->update([
            'fulfillment_status' => FulfillmentStatus::Fulfilled,
        ]);
    }

    /**
     * Check if an individual order item is unfulfilled.
     */
    private function isItemUnfulfilled(OrderItem $item): bool
    {
        $fulfilledQuantity = $this->getFulfilledQuantity($item);

        return $fulfilledQuantity < (float) $item->quantity;
    }

    /**
     * Get the fulfilled quantity for an order item.
     */
    private function getFulfilledQuantity(OrderItem $item): float
    {
        return $item->pickListItems()
            ->where('status', 'completed')
            ->sum('quantity') ?? 0;
    }
}
