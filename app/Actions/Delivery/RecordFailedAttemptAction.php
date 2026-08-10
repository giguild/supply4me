<?php

namespace App\Actions\Delivery;

use App\Enums\Delivery\DeliveryStatus;
use App\Events\Delivery\DeliveryFailed;
use App\Models\Delivery\Delivery;

class RecordFailedAttemptAction
{
    public function execute(Delivery $delivery, ?string $reason = null): Delivery
    {
        if ($delivery->status !== DeliveryStatus::OutForDelivery) {
            throw new \App\Exceptions\DeliveryCannotBeFailedException(
                'Delivery can only be marked as failed from out for delivery status.'
            );
        }

        $delivery->update([
            'status' => DeliveryStatus::FailedAttempt,
            'failure_reason' => $reason,
        ]);

        event(new DeliveryFailed($delivery, $reason));

        return $delivery->fresh();
    }
}
