<?php

namespace App\Policies;

use App\Models\PriceList;
use App\Models\User;

class PriceListPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('price_list.view');
    }

    public function view(User $user, PriceList $priceList): bool
    {
        return $user->hasPermissionTo('price_list.view')
            && $user->company_id === $priceList->company_id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('price_list.create');
    }

    public function update(User $user, PriceList $priceList): bool
    {
        return $user->hasPermissionTo('price_list.update')
            && $user->company_id === $priceList->company_id;
    }

    public function delete(User $user, PriceList $priceList): bool
    {
        return $user->hasPermissionTo('price_list.delete')
            && $user->company_id === $priceList->company_id;
    }
}
