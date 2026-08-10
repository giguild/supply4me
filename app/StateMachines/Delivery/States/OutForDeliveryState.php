<?php

declare(strict_types=1);

namespace App\StateMachines\Delivery\States;

use App\StateMachines\Delivery\DeliveryState;

class OutForDeliveryState extends DeliveryState
{
    protected string $name = 'out_for_delivery';
    protected string $label = 'Out for Delivery';
    protected string $color = '#8b5cf6';

    public function canTransitionTo(string $state): bool
    {
        return in_array($state, ['delivered', 'failed_attempt', 'partial_delivery']);
    }
}
