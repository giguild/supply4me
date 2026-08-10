<?php

declare(strict_types=1);

namespace App\StateMachines\Stock\States;

use App\StateMachines\Stock\StockState;

class ReservedState extends StockState
{
    protected string $name = 'reserved';
    protected string $label = 'Reserved';
    protected string $color = '#f59e0b';

    public function canTransitionTo(string $state): bool
    {
        return in_array($state, ['allocated', 'released']);
    }
}
