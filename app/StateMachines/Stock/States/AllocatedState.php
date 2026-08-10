<?php

declare(strict_types=1);

namespace App\StateMachines\Stock\States;

use App\StateMachines\Stock\StockState;

class AllocatedState extends StockState
{
    protected string $name = 'allocated';
    protected string $label = 'Allocated';
    protected string $color = '#3b82f6';

    public function canTransitionTo(string $state): bool
    {
        return in_array($state, ['shipped']);
    }
}
