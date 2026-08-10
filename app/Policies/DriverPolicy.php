<?php

namespace App\Policies;

use App\Models\Driver;
use App\Models\User;

class DriverPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('driver.view');
    }

    public function view(User $user, Driver $driver): bool
    {
        return $user->hasPermissionTo('driver.view')
            && $user->company_id === $driver->company_id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('driver.create');
    }

    public function update(User $user, Driver $driver): bool
    {
        return $user->hasPermissionTo('driver.update')
            && $user->company_id === $driver->company_id;
    }

    public function delete(User $user, Driver $driver): bool
    {
        return $user->hasPermissionTo('driver.delete')
            && $user->company_id === $driver->company_id;
    }
}
