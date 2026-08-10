<?php

namespace App\Policies;

use App\Models\StockAdjustment;
use App\Models\User;

class StockAdjustmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('stock_adjustment.view');
    }

    public function view(User $user, StockAdjustment $stockAdjustment): bool
    {
        return $user->hasPermissionTo('stock_adjustment.view')
            && $user->company_id === $stockAdjustment->company_id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('stock_adjustment.create');
    }

    public function approve(User $user, StockAdjustment $stockAdjustment): bool
    {
        return $user->hasPermissionTo('stock_adjustment.approve')
            && $user->company_id === $stockAdjustment->company_id;
    }

    public function reject(User $user, StockAdjustment $stockAdjustment): bool
    {
        return $user->hasPermissionTo('stock_adjustment.reject')
            && $user->company_id === $stockAdjustment->company_id;
    }
}
