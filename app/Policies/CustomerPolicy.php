<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;

class CustomerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('customer.view');
    }

    public function view(User $user, Customer $customer): bool
    {
        return $user->hasPermissionTo('customer.view')
            && $user->company_id === $customer->company_id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('customer.create');
    }

    public function update(User $user, Customer $customer): bool
    {
        return $user->hasPermissionTo('customer.update')
            && $user->company_id === $customer->company_id;
    }

    public function delete(User $user, Customer $customer): bool
    {
        return $user->hasPermissionTo('customer.delete')
            && $user->company_id === $customer->company_id;
    }

    public function updateCreditStatus(User $user, Customer $customer): bool
    {
        return $user->hasPermissionTo('customer.update_credit_status')
            && $user->company_id === $customer->company_id;
    }
}
