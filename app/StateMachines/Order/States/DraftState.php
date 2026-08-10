<?php

declare(strict_types=1);

namespace App\StateMachines\Order\States;

use App\StateMachines\Order\OrderState;

class DraftState extends OrderState
{
    protected string $name = 'draft';
    protected string $label = 'Draft';
    protected string $color = '#9ca3af';

    public function canTransitionTo(string $state): bool
    {
        return in_array($state, ['pending', 'cancelled']);
    }
}
