<?php

namespace App\Listeners\Delivery;

use App\Events\Delivery\DriverAssigned;
use App\Mail\GenericNotificationMail;
use App\Models\Delivery\Driver;
use Illuminate\Support\Facades\Mail;

class NotifyDriverOfAssignment
{
    public function handle(DriverAssigned $event): void
    {
        /** @var Driver $driver */
        $driver = $event->driver->load('user');

        if ($driver->user && $driver->user->email) {
            Mail::to($driver->user->email)->send(new GenericNotificationMail([
                'title' => 'New Delivery Assigned To You',
                'message' => 'Delivery ' . $event->delivery->delivery_number . ' has been assigned to you.',
                'action_url' => route('deliveries.show', $event->delivery->id),
                'action_label' => 'View Delivery',
            ]));
        }
    }
}