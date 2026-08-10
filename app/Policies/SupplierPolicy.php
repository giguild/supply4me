<?php

namespace App\Policies;

use App\Models\Supplier;
use App\Models\User;

class SupplierPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('supplier.view');
    }

    public function view(User $user, Supplier $supplier): bool
    {
        return $user->hasPermissionTo('supplier.view')
            && $user->company_id === $supplier->company_id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('supplier.create');
    }

    public function update(User $user, Supplier $supplier): bool
    {
        return $user->hasPermissionTo('supplier.update')
            && $user->company_id === $supplier->company_id;
    }

    public function delete(User $user, Supplier $supplier): bool
    {
        return $user->hasPermissionTo('supplier.delete')
            && $user->company_id === $supplier->company_id;
    }
}
