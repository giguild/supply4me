<?php

declare(strict_types=1);

namespace App\StateMachines\Invoice\States;

use App\StateMachines\Invoice\InvoiceState;

class OverdueState extends InvoiceState
{
    protected string $name = 'overdue';
    protected string $label = 'Overdue';
    protected string $color = '#ef4444';

    public function canTransitionTo(string $state): bool
    {
        return in_array($state, ['paid', 'cancelled']);
    }
}
