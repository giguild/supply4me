<?php

declare(strict_types=1);

namespace App\StateMachines\Delivery\States;

use App\StateMachines\Delivery\DeliveryState;

class AssignedState extends DeliveryState
{
    protected string $name = 'assigned';
    protected string $label = 'Assigned';
    protected string $color = '#3b82f6';

    public function canTransitionTo(string $state): bool
    {
        return in_array($state, ['out_for_delivery', 'cancelled']);
    }
}
