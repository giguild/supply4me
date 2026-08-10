<?php

declare(strict_types=1);

namespace App\StateMachines\Order\States;

use App\StateMachines\Order\OrderState;

class PackingState extends OrderState
{
    protected string $name = 'packing';
    protected string $label = 'Packing';
    protected string $color = '#ec4899';

    public function canTransitionTo(string $state): bool
    {
        return in_array($state, ['ready_to_ship']);
    }
}
