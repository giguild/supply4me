<?php

declare(strict_types=1);

namespace App\StateMachines\Order\States;

use App\StateMachines\Order\OrderState;

class OnHoldState extends OrderState
{
    protected string $name = 'on_hold';
    protected string $label = 'On Hold';
    protected string $color = '#f97316';

    public function canTransitionTo(string $state): bool
    {
        return in_array($state, ['processing', 'cancelled']);
    }
}
