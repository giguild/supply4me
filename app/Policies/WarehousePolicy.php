<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Warehouse;

class WarehousePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('warehouse.view');
    }

    public function view(User $user, Warehouse $warehouse): bool
    {
        return $user->hasPermissionTo('warehouse.view')
            && $user->company_id === $warehouse->company_id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('warehouse.create');
    }

    public function update(User $user, Warehouse $warehouse): bool
    {
        return $user->hasPermissionTo('warehouse.update')
            && $user->company_id === $warehouse->company_id;
    }

    public function delete(User $user, Warehouse $warehouse): bool
    {
        return $user->hasPermissionTo('warehouse.delete')
            && $user->company_id === $warehouse->company_id;
    }
}
