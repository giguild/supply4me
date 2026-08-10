<?php

declare(strict_types=1);

namespace App\StateMachines\Order\States;

use App\StateMachines\Order\OrderState;

class ShippedState extends OrderState
{
    protected string $name = 'shipped';
    protected string $label = 'Shipped';
    protected string $color = '#14b8a6';

    public function canTransitionTo(string $state): bool
    {
        return in_array($state, ['delivered']);
    }
}
