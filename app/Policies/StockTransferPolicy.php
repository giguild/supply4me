<?php

namespace App\Policies;

use App\Models\StockTransfer;
use App\Models\User;

class StockTransferPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('stock_transfer.view');
    }

    public function view(User $user, StockTransfer $stockTransfer): bool
    {
        return $user->hasPermissionTo('stock_transfer.view')
            && $user->company_id === $stockTransfer->company_id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('stock_transfer.create');
    }

    public function approve(User $user, StockTransfer $stockTransfer): bool
    {
        return $user->hasPermissionTo('stock_transfer.approve')
            && $user->company_id === $stockTransfer->company_id;
    }

    public function ship(User $user, StockTransfer $stockTransfer): bool
    {
        return $user->hasPermissionTo('stock_transfer.ship')
            && $user->company_id === $stockTransfer->company_id;
    }

    public function receive(User $user, StockTransfer $stockTransfer): bool
    {
        return $user->hasPermissionTo('stock_transfer.receive')
            && $user->company_id === $stockTransfer->company_id;
    }
}
