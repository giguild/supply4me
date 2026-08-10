<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('payment.view');
    }

    public function view(User $user, Payment $payment): bool
    {
        return $user->hasPermissionTo('payment.view')
            && $user->company_id === $payment->company_id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('payment.create');
    }

    public function update(User $user, Payment $payment): bool
    {
        return $user->hasPermissionTo('payment.update')
            && $user->company_id === $payment->company_id;
    }

    public function delete(User $user, Payment $payment): bool
    {
        return $user->hasPermissionTo('payment.delete')
            && $user->company_id === $payment->company_id;
    }

    public function approve(User $user, Payment $payment): bool
    {
        return $user->hasPermissionTo('payment.approve')
            && $user->company_id === $payment->company_id;
    }

    public function reject(User $user, Payment $payment): bool
    {
        return $user->hasPermissionTo('payment.reject')
            && $user->company_id === $payment->company_id;
    }

    public function refund(User $user, Payment $payment): bool
    {
        return $user->hasPermissionTo('payment.refund')
            && $user->company_id === $payment->company_id;
    }
}
