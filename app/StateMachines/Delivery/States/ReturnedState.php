<?php

declare(strict_types=1);

namespace App\StateMachines\Delivery\States;

use App\StateMachines\Delivery\DeliveryState;

class ReturnedState extends DeliveryState
{
    protected string $name = 'returned';
    protected string $label = 'Returned';
    protected string $color = '#6b7280';

    public function canTransitionTo(string $state): bool
    {
        return false;
    }
}
