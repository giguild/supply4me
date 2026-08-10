<?php

declare(strict_types=1);

namespace App\StateMachines\Delivery\States;

use App\StateMachines\Delivery\DeliveryState;

class FailedAttemptState extends DeliveryState
{
    protected string $name = 'failed_attempt';
    protected string $label = 'Failed Attempt';
    protected string $color = '#ef4444';

    public function canTransitionTo(string $state): bool
    {
        return in_array($state, ['assigned', 'cancelled']);
    }
}
