<?php

declare(strict_types=1);

namespace App\StateMachines\Order\States;

use App\StateMachines\Order\OrderState;

class ProcessingState extends OrderState
{
    protected string $name = 'processing';
    protected string $label = 'Processing';
    protected string $color = '#3b82f6';

    public function canTransitionTo(string $state): bool
    {
        return in_array($state, ['picking', 'cancelled', 'on_hold']);
    }
}
