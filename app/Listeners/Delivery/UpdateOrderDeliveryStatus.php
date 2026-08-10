<?php

namespace App\Listeners\Delivery;

use App\Events\Delivery\DeliveryCompleted;
use App\Enums\Orders\FulfillmentStatus;
use App\Models\Orders\Order;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateOrderDeliveryStatus implements ShouldQueue
{
    public function handle(DeliveryCompleted $event): void
    {
        /** @var Order $order */
        $order = $event->delivery->order;

        if ($order) {
            $totalOrdered = $order->items()->sum('quantity');
            $totalDelivered = $event->delivery->items()->sum('quantity_delivered');

            $fulfillmentStatus = match (true) {
                $totalDelivered >= $totalOrdered => FulfillmentStatus::Fulfilled,
                $totalDelivered > 0 => FulfillmentStatus::PartiallyFulfilled,
                default => FulfillmentStatus::Pending,
            };

            $order->update(['fulfillment_status' => $fulfillmentStatus]);
        }
    }
}
