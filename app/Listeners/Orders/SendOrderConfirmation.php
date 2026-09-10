<?php

namespace App\Listeners\Orders;

use App\Events\Orders\OrderConfirmed;
use App\Models\Orders\Order;
use App\Mail\OrderConfirmedMail;
use Illuminate\Support\Facades\Mail;

class SendOrderConfirmation
{
    public function handle(OrderConfirmed $event): void
    {
        /** @var Order $order */
        $order = $event->order->load('customer');

        if ($order->customer && $order->customer->email) {
            Mail::to($order->customer->email)->send(new OrderConfirmedMail($order));
        }
    }
}