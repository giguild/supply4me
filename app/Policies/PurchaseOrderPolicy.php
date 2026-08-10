<?php

namespace App\Policies;

use App\Models\PurchaseOrder;
use App\Models\User;

class PurchaseOrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('purchase_order.view');
    }

    public function view(User $user, PurchaseOrder $purchaseOrder): bool
    {
        return $user->hasPermissionTo('purchase_order.view')
            && $user->company_id === $purchaseOrder->company_id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('purchase_order.create');
    }

    public function update(User $user, PurchaseOrder $purchaseOrder): bool
    {
        return $user->hasPermissionTo('purchase_order.update')
            && $user->company_id === $purchaseOrder->company_id;
    }

    public function delete(User $user, PurchaseOrder $purchaseOrder): bool
    {
        return $user->hasPermissionTo('purchase_order.delete')
            && $user->company_id === $purchaseOrder->company_id;
    }

    public function approve(User $user, PurchaseOrder $purchaseOrder): bool
    {
        return $user->hasPermissionTo('purchase_order.approve')
            && $user->company_id === $purchaseOrder->company_id;
    }
}
