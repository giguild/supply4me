<?php

namespace App\Actions\Customers;

use App\Enums\Customers\CustomerType;
use App\Events\Customers\CustomerUpdated;
use App\Models\Customers\Customer;

class UpdateCustomerAction
{
    public function execute(Customer $customer, array $data): Customer
    {
        if (isset($data['customer_type'])) {
            $data['customer_type'] = CustomerType::from($data['customer_type']);
        }

        $customer->update($data);

        event(new CustomerUpdated($customer));

        return $customer->fresh();
    }
}
