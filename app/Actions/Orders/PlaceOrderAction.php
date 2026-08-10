<?php

namespace App\Actions\Orders;

use App\Enums\Orders\OrderStatus;
use App\Events\Orders\OrderStatusChanged;
use App\Models\Orders\Order;
use App\Exceptions\OrderCannotBePlacedException;

class PlaceOrderAction
{
    public function execute(Order $order): Order
    {
        if ($order->status !== OrderStatus::Draft) {
            throw new OrderCannotBePlacedException(
                'Order can only be placed from draft status.'
            );
        }

        $oldStatus = $order->status;

        $order->update([
            'status' => OrderStatus::Pending,
        ]);

        event(new OrderStatusChanged(
            $order,
            $oldStatus,
            OrderStatus::Pending,
            auth()->user()
        ));

        return $order->fresh();
    }
}
