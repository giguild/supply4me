<?php

declare(strict_types=1);

namespace App\StateMachines\Invoice\States;

use App\StateMachines\Invoice\InvoiceState;

class VoidState extends InvoiceState
{
    protected string $name = 'void';
    protected string $label = 'Void';
    protected string $color = '#6b7280';

    public function canTransitionTo(string $state): bool
    {
        return false;
    }
}
