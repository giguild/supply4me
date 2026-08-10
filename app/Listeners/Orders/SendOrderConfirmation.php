<?php

namespace App\Listeners\Orders;

use App\Events\Orders\OrderConfirmed;
use App\Models\Core\User;
use App\Models\Orders\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendOrderConfirmation implements ShouldQueue
{
    public function handle(OrderConfirmed $event): void
    {
        /** @var Order $order */
        $order = $event->order->load('customer');

        if ($order->customer && $order->customer->email) {
            Mail::to($order->customer->email)->send(new \App\Mail\OrderConfirmedMail($order));
        }
    }
}
