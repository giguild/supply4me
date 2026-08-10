<?php

declare(strict_types=1);

namespace App\StateMachines\Delivery\States;

use App\StateMachines\Delivery\DeliveryState;

class CancelledState extends DeliveryState
{
    protected string $name = 'cancelled';
    protected string $label = 'Cancelled';
    protected string $color = '#ef4444';

    public function canTransitionTo(string $state): bool
    {
        return false;
    }
}
