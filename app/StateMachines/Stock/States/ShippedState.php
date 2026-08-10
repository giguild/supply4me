<?php

declare(strict_types=1);

namespace App\StateMachines\Stock\States;

use App\StateMachines\Stock\StockState;

class ShippedState extends StockState
{
    protected string $name = 'shipped';
    protected string $label = 'Shipped';
    protected string $color = '#14b8a6';

    public function canTransitionTo(string $state): bool
    {
        return false;
    }
}
