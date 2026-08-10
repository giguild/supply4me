<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('product.view');
    }

    public function view(User $user, Product $product): bool
    {
        return $user->hasPermissionTo('product.view')
            && $user->company_id === $product->company_id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('product.create');
    }

    public function update(User $user, Product $product): bool
    {
        return $user->hasPermissionTo('product.update')
            && $user->company_id === $product->company_id;
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->hasPermissionTo('product.delete')
            && $user->company_id === $product->company_id;
    }
}
