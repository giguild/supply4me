<?php

declare(strict_types=1);

namespace App\StateMachines\Stock\States;

use App\StateMachines\Stock\StockState;

class ReleasedState extends StockState
{
    protected string $name = 'released';
    protected string $label = 'Released';
    protected string $color = '#ef4444';

    public function canTransitionTo(string $state): bool
    {
        return in_array($state, ['available']);
    }
}
