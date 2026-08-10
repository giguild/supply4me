<?php

declare(strict_types=1);

namespace App\StateMachines\Payment\States;

use App\StateMachines\Payment\PaymentState;

class CompletedState extends PaymentState
{
    protected string $name = 'completed';
    protected string $label = 'Completed';
    protected string $color = '#22c55e';

    public function canTransitionTo(string $state): bool
    {
        return in_array($state, ['refunded']);
    }
}
