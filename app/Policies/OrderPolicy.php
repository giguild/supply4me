<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('order.view');
    }

    public function view(User $user, Order $order): bool
    {
        return $user->hasPermissionTo('order.view')
            && $user->company_id === $order->company_id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('order.create');
    }

    public function update(User $user, Order $order): bool
    {
        return $user->hasPermissionTo('order.update')
            && $user->company_id === $order->company_id;
    }

    public function delete(User $user, Order $order): bool
    {
        return $user->hasPermissionTo('order.delete')
            && $user->company_id === $order->company_id;
    }

    public function confirm(User $user, Order $order): bool
    {
        return $user->hasPermissionTo('order.confirm')
            && $user->company_id === $order->company_id;
    }

    public function cancel(User $user, Order $order): bool
    {
        return $user->hasPermissionTo('order.cancel')
            && $user->company_id === $order->company_id;
    }

    public function hold(User $user, Order $order): bool
    {
        return $user->hasPermissionTo('order.hold')
            && $user->company_id === $order->company_id;
    }

    public function release(User $user, Order $order): bool
    {
        return $user->hasPermissionTo('order.release')
            && $user->company_id === $order->company_id;
    }
}
