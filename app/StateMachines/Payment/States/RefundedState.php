<?php

declare(strict_types=1);

namespace App\StateMachines\Payment\States;

use App\StateMachines\Payment\PaymentState;

class RefundedState extends PaymentState
{
    protected string $name = 'refunded';
    protected string $label = 'Refunded';
    protected string $color = '#f97316';

    public function canTransitionTo(string $state): bool
    {
        return false;
    }
}
