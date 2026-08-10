<?php

declare(strict_types=1);

namespace App\StateMachines\Delivery\States;

use App\StateMachines\Delivery\DeliveryState;

class PartialDeliveryState extends DeliveryState
{
    protected string $name = 'partial_delivery';
    protected string $label = 'Partial Delivery';
    protected string $color = '#f97316';

    public function canTransitionTo(string $state): bool
    {
        return in_array($state, ['delivered']);
    }
}
