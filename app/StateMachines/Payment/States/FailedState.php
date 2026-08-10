<?php

declare(strict_types=1);

namespace App\StateMachines\Payment\States;

use App\StateMachines\Payment\PaymentState;

class FailedState extends PaymentState
{
    protected string $name = 'failed';
    protected string $label = 'Failed';
    protected string $color = '#ef4444';

    public function canTransitionTo(string $state): bool
    {
        return in_array($state, ['pending', 'cancelled']);
    }
}
