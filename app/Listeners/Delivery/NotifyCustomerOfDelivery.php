<?php

namespace App\Listeners\Delivery;

use App\Events\Delivery\DeliveryCompleted;
use App\Events\Delivery\DeliveryFailed;
use App\Mail\DeliveryUpdateMail;
use App\Models\Delivery\Delivery;
use Illuminate\Support\Facades\Mail;

class NotifyCustomerOfDelivery
{
    public function handle(DeliveryCompleted|DeliveryFailed $event): void
    {
        /** @var Delivery $delivery */
        $delivery = $event->delivery->load('customer', 'order');

        if (!$delivery->customer || !$delivery->customer->email) {
            return;
        }

        if ($event instanceof DeliveryCompleted) {
            $mail = new DeliveryUpdateMail($delivery, 'has been successfully delivered');
        } else {
            $mail = new DeliveryUpdateMail($delivery, 'could not be completed', $event->reason);
        }

        Mail::to($delivery->customer->email)->send($mail);
    }
}