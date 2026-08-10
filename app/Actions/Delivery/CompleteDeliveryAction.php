<?php

namespace App\Actions\Delivery;

use App\Enums\Delivery\DeliveryStatus;
use App\Enums\Orders\FulfillmentStatus;
use App\Events\Delivery\DeliveryCompleted;
use App\Models\Delivery\Delivery;
use App\Models\Delivery\DeliveryItem;
use App\Models\Orders\Order;
use Illuminate\Support\Facades\DB;

class CompleteDeliveryAction
{
    public function execute(Delivery $delivery, array $data): Delivery
    {
        if ($delivery->status !== DeliveryStatus::OutForDelivery) {
            throw new \App\Exceptions\DeliveryCannotBeCompletedException(
                'Delivery can only be completed from out for delivery status.'
            );
        }

        return DB::transaction(function () use ($delivery, $data) {
            $deliveryItems = [];

            if (! empty($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $itemData) {
                    $deliveryItem = DeliveryItem::create([
                        'delivery_id' => $delivery->id,
                        'order_item_id' => $itemData['order_item_id'],
                        'product_id' => $itemData['product_id'],
                        'quantity' => $itemData['quantity'],
                        'quantity_delivered' => $itemData['quantity_delivered'],
                        'condition' => $itemData['condition'] ?? 'good',
                        'notes' => $itemData['notes'] ?? null,
                    ]);
                    $deliveryItems[] = $deliveryItem;
                }
            }

            $delivery->update([
                'status' => DeliveryStatus::Delivered,
                'actual_delivery_date' => $data['delivery_date'] ?? now()->toDateString(),
                'delivery_time' => now(),
                'proof_of_delivery' => $data['proof_of_delivery'] ?? null,
                'delivery_notes' => $data['delivery_notes'] ?? null,
            ]);

            $order = Order::find($delivery->order_id);
            if ($order) {
                $order->update([
                    'fulfillment_status' => FulfillmentStatus::Delivered,
                ]);
            }

            event(new DeliveryCompleted($delivery, $deliveryItems));

            return $delivery->fresh();
        });
    }
}
