<?php

declare(strict_types=1);

namespace App\StateMachines\Order\States;

use App\StateMachines\Order\OrderState;

class DeliveredState extends OrderState
{
    protected string $name = 'delivered';
    protected string $label = 'Delivered';
    protected string $color = '#22c55e';

    public function canTransitionTo(string $state): bool
    {
        return in_array($state, ['completed']);
    }
}
