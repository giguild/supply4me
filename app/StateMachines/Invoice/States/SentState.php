<?php

declare(strict_types=1);

namespace App\StateMachines\Invoice\States;

use App\StateMachines\Invoice\InvoiceState;

class SentState extends InvoiceState
{
    protected string $name = 'sent';
    protected string $label = 'Sent';
    protected string $color = '#3b82f6';

    public function canTransitionTo(string $state): bool
    {
        return in_array($state, ['viewed', 'overdue']);
    }
}
