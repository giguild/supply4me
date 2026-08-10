<?php

namespace App\Actions\Orders;

use App\Enums\Orders\FulfillmentStatus;
use App\Enums\Orders\OrderStatus;
use App\Enums\Orders\PaymentStatus;
use App\Events\Orders\OrderCancelled;
use App\Models\Core\User;
use App\Models\Inventory\StockItem;
use App\Models\Inventory\StockMovement;
use App\Models\Orders\Order;
use Illuminate\Support\Facades\DB;

class CancelOrderAction
{
    public function execute(Order $order, User $user, ?string $reason = null): Order
    {
        if (in_array($order->status, [OrderStatus::Cancelled, OrderStatus::Completed])) {
            throw new \App\Exceptions\OrderCannotBeCancelledException(
                'Order cannot be cancelled in its current status.'
            );
        }

        return DB::transaction(function () use ($order, $user, $reason) {
            $order->load('items');

            foreach ($order->items as $item) {
                $stockItems = StockItem::where('product_id', $item->product_id)
                    ->where('company_id', $order->company_id)
                    ->get();

                foreach ($stockItems as $stockItem) {
                    if ($stockItem->quantity_reserved > 0) {
                        $quantityToRelease = min($stockItem->quantity_reserved, $item->quantity);

                        $stockItem->decrement('quantity_reserved', $quantityToRelease);
                        $stockItem->incrementVersion();

                        StockMovement::create([
                            'company_id' => $order->company_id,
                            'stock_item_id' => $stockItem->id,
                            'movement_type' => 'release',
                            'quantity' => $quantityToRelease,
                            'quantity_before' => $stockItem->quantity_reserved + $quantityToRelease,
                            'quantity_after' => $stockItem->quantity_reserved,
                            'reference_type' => Order::class,
                            'reference_id' => $order->id,
                            'reason' => 'Order cancelled',
                            'performed_by' => $user->id,
                        ]);
                    }
                }
            }

            $order->update([
                'status' => OrderStatus::Cancelled,
                'payment_status' => PaymentStatus::Cancelled,
                'fulfillment_status' => FulfillmentStatus::Cancelled,
            ]);

            event(new OrderCancelled($order, $user, $reason));

            return $order->fresh();
        });
    }
}
