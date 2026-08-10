<?php

declare(strict_types=1);

namespace App\StateMachines\Delivery\States;

use App\StateMachines\Delivery\DeliveryState;

class DeliveredState extends DeliveryState
{
    protected string $name = 'delivered';
    protected string $label = 'Delivered';
    protected string $color = '#22c55e';

    public function canTransitionTo(string $state): bool
    {
        return in_array($state, ['returned']);
    }
}
