<?php

declare(strict_types=1);

namespace App\StateMachines\Stock\States;

use App\StateMachines\Stock\StockState;

class AvailableState extends StockState
{
    protected string $name = 'available';
    protected string $label = 'Available';
    protected string $color = '#22c55e';

    public function canTransitionTo(string $state): bool
    {
        return in_array($state, ['reserved']);
    }
}
