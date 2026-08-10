<?php

declare(strict_types=1);

namespace App\StateMachines\Invoice\States;

use App\StateMachines\Invoice\InvoiceState;

class CancelledState extends InvoiceState
{
    protected string $name = 'cancelled';
    protected string $label = 'Cancelled';
    protected string $color = '#ef4444';

    public function canTransitionTo(string $state): bool
    {
        return false;
    }
}
