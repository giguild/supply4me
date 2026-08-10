<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('invoice.view');
    }

    public function view(User $user, Invoice $invoice): bool
    {
        return $user->hasPermissionTo('invoice.view')
            && $user->company_id === $invoice->company_id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('invoice.create');
    }

    public function update(User $user, Invoice $invoice): bool
    {
        return $user->hasPermissionTo('invoice.update')
            && $user->company_id === $invoice->company_id;
    }

    public function delete(User $user, Invoice $invoice): bool
    {
        return $user->hasPermissionTo('invoice.delete')
            && $user->company_id === $invoice->company_id;
    }

    public function send(User $user, Invoice $invoice): bool
    {
        return $user->hasPermissionTo('invoice.send')
            && $user->company_id === $invoice->company_id;
    }

    public function void(User $user, Invoice $invoice): bool
    {
        return $user->hasPermissionTo('invoice.void')
            && $user->company_id === $invoice->company_id;
    }
}
