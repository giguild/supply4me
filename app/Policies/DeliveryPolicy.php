<?php

namespace App\Policies;

use App\Models\Delivery;
use App\Models\User;

class DeliveryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('delivery.view');
    }

    public function view(User $user, Delivery $delivery): bool
    {
        return $user->hasPermissionTo('delivery.view')
            && $user->company_id === $delivery->company_id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('delivery.create');
    }

    public function update(User $user, Delivery $delivery): bool
    {
        return $user->hasPermissionTo('delivery.update')
            && $user->company_id === $delivery->company_id;
    }

    public function assignDriver(User $user, Delivery $delivery): bool
    {
        return $user->hasPermissionTo('delivery.assign_driver')
            && $user->company_id === $delivery->company_id;
    }

    public function complete(User $user, Delivery $delivery): bool
    {
        return $user->hasPermissionTo('delivery.complete')
            && $user->company_id === $delivery->company_id;
    }
}
