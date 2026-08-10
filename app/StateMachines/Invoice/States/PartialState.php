<?php

declare(strict_types=1);

namespace App\StateMachines\Invoice\States;

use App\StateMachines\Invoice\InvoiceState;

class PartialState extends InvoiceState
{
    protected string $name = 'partial';
    protected string $label = 'Partial';
    protected string $color = '#f97316';

    public function canTransitionTo(string $state): bool
    {
        return in_array($state, ['paid', 'overdue']);
    }
}
