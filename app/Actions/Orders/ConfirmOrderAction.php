<?php

namespace App\Actions\Orders;

use App\Enums\Orders\OrderStatus;
use App\Events\Orders\OrderConfirmed;
use App\Models\Core\User;
use App\Models\Orders\Order;

class ConfirmOrderAction
{
    public function execute(Order $order, User $user): Order
    {
        if ($order->status !== OrderStatus::Pending) {
            throw new \App\Exceptions\OrderCannotBeConfirmedException(
                'Order can only be confirmed from pending status.'
            );
        }

        $order->update([
            'status' => OrderStatus::Confirmed,
        ]);

        event(new OrderConfirmed($order, $user));

        return $order->fresh();
    }
}
