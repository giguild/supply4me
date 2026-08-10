<?php

namespace App\Policies;

use App\Models\Branch;
use App\Models\User;

class BranchPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('branch.view');
    }

    public function view(User $user, Branch $branch): bool
    {
        return $user->hasPermissionTo('branch.view')
            && $user->company_id === $branch->company_id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('branch.create');
    }

    public function update(User $user, Branch $branch): bool
    {
        return $user->hasPermissionTo('branch.update')
            && $user->company_id === $branch->company_id;
    }

    public function delete(User $user, Branch $branch): bool
    {
        return $user->hasPermissionTo('branch.delete')
            && $user->company_id === $branch->company_id;
    }
}
