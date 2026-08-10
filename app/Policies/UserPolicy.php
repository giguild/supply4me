<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('user.view');
    }

    public function view(User $user, User $model): bool
    {
        return $user->hasPermissionTo('user.view')
            && $user->company_id === $model->company_id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('user.create');
    }

    public function update(User $user, User $model): bool
    {
        return $user->hasPermissionTo('user.update')
            && $user->company_id === $model->company_id;
    }

    public function delete(User $user, User $model): bool
    {
        return $user->hasPermissionTo('user.delete')
            && $user->company_id === $model->company_id;
    }
}
