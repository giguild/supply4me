<?php

namespace App\Policies;

use App\Models\ProductCategory;
use App\Models\User;

class ProductCategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('product_category.view');
    }

    public function view(User $user, ProductCategory $productCategory): bool
    {
        return $user->hasPermissionTo('product_category.view')
            && $user->company_id === $productCategory->company_id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('product_category.create');
    }

    public function update(User $user, ProductCategory $productCategory): bool
    {
        return $user->hasPermissionTo('product_category.update')
            && $user->company_id === $productCategory->company_id;
    }

    public function delete(User $user, ProductCategory $productCategory): bool
    {
        return $user->hasPermissionTo('product_category.delete')
            && $user->company_id === $productCategory->company_id;
    }
}
