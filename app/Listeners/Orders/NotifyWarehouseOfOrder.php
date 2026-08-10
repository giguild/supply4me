<?php

namespace App\Listeners\Orders;

use App\Events\Orders\OrderConfirmed;
use App\Models\Core\User;
use App\Models\Orders\Order;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyWarehouseOfOrder implements ShouldQueue
{
    public function handle(OrderConfirmed $event): void
    {
        /** @var Order $order */
        $order = $event->order->load('warehouse.users');

        $warehouseUsers = $order->warehouse->users ?? collect();

        foreach ($warehouseUsers as $warehouseUser) {
            $warehouseUser->notify(new \App\Notifications\WarehouseOrderNotification($order));
        }
    }
}
