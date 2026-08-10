<?php

namespace App\Policies;

use App\Models\StockItem;
use App\Models\User;

class StockItemPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('stock_item.view');
    }

    public function view(User $user, StockItem $stockItem): bool
    {
        return $user->hasPermissionTo('stock_item.view')
            && $user->company_id === $stockItem->company_id;
    }

    public function update(User $user, StockItem $stockItem): bool
    {
        return $user->hasPermissionTo('stock_item.update')
            && $user->company_id === $stockItem->company_id;
    }
}
