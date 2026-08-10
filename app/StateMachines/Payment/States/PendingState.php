<?php

declare(strict_types=1);

namespace App\StateMachines\Payment\States;

use App\StateMachines\Payment\PaymentState;

class PendingState extends PaymentState
{
    protected string $name = 'pending';
    protected string $label = 'Pending';
    protected string $color = '#f59e0b';

    public function canTransitionTo(string $state): bool
    {
        return in_array($state, ['processing', 'cancelled']);
    }
}
