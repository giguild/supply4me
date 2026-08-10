<?php

namespace App\Actions\Customers;

use App\Enums\Customers\CreditStatus;
use App\Events\Customers\CreditStatusChanged;
use App\Models\Customers\Customer;

class UpdateCreditStatusAction
{
    public function execute(Customer $customer, CreditStatus $newStatus): Customer
    {
        $oldStatus = $customer->credit_status;

        if ($oldStatus === $newStatus) {
            return $customer;
        }

        $customer->update([
            'credit_status' => $newStatus,
        ]);

        event(new CreditStatusChanged($customer, $oldStatus, $newStatus));

        return $customer->fresh();
    }
}
