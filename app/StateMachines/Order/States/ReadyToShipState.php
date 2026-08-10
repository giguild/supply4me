<?php

declare(strict_types=1);

namespace App\StateMachines\Order\States;

use App\StateMachines\Order\OrderState;

class ReadyToShipState extends OrderState
{
    protected string $name = 'ready_to_ship';
    protected string $label = 'Ready to Ship';
    protected string $color = '#06b6d4';

    public function canTransitionTo(string $state): bool
    {
        return in_array($state, ['shipped']);
    }
}
