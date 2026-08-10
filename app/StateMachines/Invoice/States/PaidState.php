<?php

declare(strict_types=1);

namespace App\StateMachines\Invoice\States;

use App\StateMachines\Invoice\InvoiceState;

class PaidState extends InvoiceState
{
    protected string $name = 'paid';
    protected string $label = 'Paid';
    protected string $color = '#22c55e';

    public function canTransitionTo(string $state): bool
    {
        return false;
    }
}
