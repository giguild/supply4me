<?php

namespace App\Listeners\Delivery;

use App\Events\Delivery\DeliveryCompleted;
use App\Events\Delivery\DeliveryFailed;
use App\Models\Delivery\Delivery;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyCustomerOfDelivery implements ShouldQueue
{
    public function handle(DeliveryCompleted|DeliveryFailed $event): void
    {
        /** @var Delivery $delivery */
        $delivery = $event->delivery->load('customer', 'order');

        if ($delivery->customer && $delivery->customer->email) {
            $notification = match (true) {
                $event instanceof DeliveryCompleted => new \App\Notifications\DeliveryCompletedNotification($delivery),
                $event instanceof DeliveryFailed => new \App\Notifications\DeliveryFailedNotification($delivery, $event->reason),
            };

            $delivery->customer->notify($notification);
        }
    }
}
