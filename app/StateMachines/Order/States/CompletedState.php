<?php

declare(strict_types=1);

namespace App\StateMachines\Order\States;

use App\StateMachines\Order\OrderState;

class CompletedState extends OrderState
{
    protected string $name = 'completed';
    protected string $label = 'Completed';
    protected string $color = '#16a34a';

    public function canTransitionTo(string $state): bool
    {
        return false;
    }
}
