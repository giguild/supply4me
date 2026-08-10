<?php

namespace App\Policies;

use App\Models\ProductBrand;
use App\Models\User;

class ProductBrandPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('product_brand.view');
    }

    public function view(User $user, ProductBrand $productBrand): bool
    {
        return $user->hasPermissionTo('product_brand.view')
            && $user->company_id === $productBrand->company_id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('product_brand.create');
    }

    public function update(User $user, ProductBrand $productBrand): bool
    {
        return $user->hasPermissionTo('product_brand.update')
            && $user->company_id === $productBrand->company_id;
    }

    public function delete(User $user, ProductBrand $productBrand): bool
    {
        return $user->hasPermissionTo('product_brand.delete')
            && $user->company_id === $productBrand->company_id;
    }
}
