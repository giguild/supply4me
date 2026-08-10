<?php

declare(strict_types=1);

namespace App\StateMachines\Invoice\States;

use App\StateMachines\Invoice\InvoiceState;

class ViewedState extends InvoiceState
{
    protected string $name = 'viewed';
    protected string $label = 'Viewed';
    protected string $color = '#14b8a6';

    public function canTransitionTo(string $state): bool
    {
        return in_array($state, ['paid', 'partial', 'overdue']);
    }
}
