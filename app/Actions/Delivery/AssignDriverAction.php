<?php

namespace App\Actions\Delivery;

use App\Enums\Delivery\DeliveryStatus;
use App\Events\Delivery\DriverAssigned;
use App\Models\Delivery\Delivery;
use App\Models\Delivery\Driver;

class AssignDriverAction
{
    public function execute(Delivery $delivery, Driver $driver): Delivery
    {
        if ($delivery->status !== DeliveryStatus::Pending && $delivery->status !== DeliveryStatus::Assigned) {
            throw new \App\Exceptions\DeliveryCannotBeAssignedException(
                'Delivery can only be assigned from pending or assigned status.'
            );
        }

        $delivery->update([
            'driver_id' => $driver->id,
            'status' => DeliveryStatus::Assigned,
        ]);

        event(new DriverAssigned($delivery, $driver));

        return $delivery->fresh();
    }
}
