<?php

declare(strict_types=1);

namespace App\StateMachines\Order\States;

use App\StateMachines\Order\OrderState;

class ConfirmedState extends OrderState
{
    protected string $name = 'confirmed';
    protected string $label = 'Confirmed';
    protected string $color = '#10b981';

    public function canTransitionTo(string $state): bool
    {
        return in_array($state, ['processing', 'cancelled']);
    }
}
