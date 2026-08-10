<?php

declare(strict_types=1);

namespace App\StateMachines\Order\States;

use App\StateMachines\Order\OrderState;

class CancelledState extends OrderState
{
    protected string $name = 'cancelled';
    protected string $label = 'Cancelled';
    protected string $color = '#ef4444';

    public function canTransitionTo(string $state): bool
    {
        return false;
    }
}
