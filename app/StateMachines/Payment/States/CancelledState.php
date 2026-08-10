<?php

declare(strict_types=1);

namespace App\StateMachines\Payment\States;

use App\StateMachines\Payment\PaymentState;

class CancelledState extends PaymentState
{
    protected string $name = 'cancelled';
    protected string $label = 'Cancelled';
    protected string $color = '#ef4444';

    public function canTransitionTo(string $state): bool
    {
        return false;
    }
}
