<?php

declare(strict_types=1);

namespace App\StateMachines\Order\States;

use App\StateMachines\Order\OrderState;

class PickingState extends OrderState
{
    protected string $name = 'picking';
    protected string $label = 'Picking';
    protected string $color = '#8b5cf6';

    public function canTransitionTo(string $state): bool
    {
        return in_array($state, ['packing']);
    }
}
