<?php

namespace App\Listeners\Delivery;

use App\Events\Delivery\DriverAssigned;
use App\Models\Delivery\Driver;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyDriverOfAssignment implements ShouldQueue
{
    public function handle(DriverAssigned $event): void
    {
        /** @var Driver $driver */
        $driver = $event->driver->load('user');

        if ($driver->user) {
            $driver->user->notify(new \App\Notifications\DriverAssignmentNotification($event->delivery));
        }
    }
}
