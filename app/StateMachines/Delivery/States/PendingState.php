<?php

declare(strict_types=1);

namespace App\StateMachines\Delivery\States;

use App\StateMachines\Delivery\DeliveryState;

class PendingState extends DeliveryState
{
    protected string $name = 'pending';
    protected string $label = 'Pending';
    protected string $color = '#f59e0b';

    public function canTransitionTo(string $state): bool
    {
        return in_array($state, ['assigned', 'cancelled']);
    }
}
