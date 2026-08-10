<?php

namespace App\Actions\Delivery;

use App\Enums\Delivery\DeliveryStatus;
use App\Events\Delivery\DeliveryStarted;
use App\Models\Delivery\Delivery;
use App\Models\Delivery\Driver;

class StartDeliveryAction
{
    public function execute(Delivery $delivery, Driver $driver): Delivery
    {
        if ($delivery->status !== DeliveryStatus::Assigned) {
            throw new \App\Exceptions\DeliveryCannotBeStartedException(
                'Delivery can only be started from assigned status.'
            );
        }

        $delivery->update([
            'status' => DeliveryStatus::OutForDelivery,
        ]);

        event(new DeliveryStarted($delivery, $driver));

        return $delivery->fresh();
    }
}
