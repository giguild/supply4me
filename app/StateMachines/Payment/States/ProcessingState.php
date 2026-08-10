<?php

declare(strict_types=1);

namespace App\StateMachines\Payment\States;

use App\StateMachines\Payment\PaymentState;

class ProcessingState extends PaymentState
{
    protected string $name = 'processing';
    protected string $label = 'Processing';
    protected string $color = '#3b82f6';

    public function canTransitionTo(string $state): bool
    {
        return in_array($state, ['completed', 'failed']);
    }
}
