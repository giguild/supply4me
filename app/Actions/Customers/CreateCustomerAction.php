<?php

namespace App\Actions\Customers;

use App\Enums\Customers\CreditStatus;
use App\Enums\Customers\CustomerStatus;
use App\Enums\Customers\CustomerType;
use App\Events\Customers\CustomerCreated;
use App\Models\Customers\Customer;
use App\Models\Customers\CustomerShippingAddress;
use Illuminate\Support\Facades\DB;

class CreateCustomerAction
{
    public function execute(array $data): Customer
    {
        return DB::transaction(function () use ($data) {
            $customer = Customer::create([
                'company_id' => $data['company_id'],
                'name' => $data['name'],
                'trade_name' => $data['trade_name'] ?? null,
                'customer_type' => CustomerType::from($data['customer_type'] ?? CustomerType::Individual->value),
                'tax_number' => $data['tax_number'] ?? null,
                'registration_number' => $data['registration_number'] ?? null,
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'mobile' => $data['mobile'] ?? null,
                'fax' => $data['fax'] ?? null,
                'website' => $data['website'] ?? null,
                'address_line_1' => $data['address_line_1'] ?? null,
                'address_line_2' => $data['address_line_2'] ?? null,
                'city' => $data['city'] ?? null,
                'state' => $data['state'] ?? null,
                'postal_code' => $data['postal_code'] ?? null,
                'country' => $data['country'] ?? null,
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'credit_limit' => $data['credit_limit'] ?? 0,
                'payment_terms_days' => $data['payment_terms_days'] ?? 30,
                'discount_percentage' => $data['discount_percentage'] ?? 0,
                'assigned_to' => $data['assigned_to'] ?? null,
                'price_list_id' => $data['price_list_id'] ?? null,
                'status' => CustomerStatus::Active,
                'credit_status' => CreditStatus::Good,
                'notes' => $data['notes'] ?? null,
                'metadata' => $data['metadata'] ?? [],
            ]);

            if (! empty($data['shipping_address'])) {
                CustomerShippingAddress::create([
                    'customer_id' => $customer->id,
                    'label' => $data['shipping_address']['label'] ?? 'Default',
                    'address_line_1' => $data['shipping_address']['address_line_1'],
                    'address_line_2' => $data['shipping_address']['address_line_2'] ?? null,
                    'city' => $data['shipping_address']['city'] ?? null,
                    'state' => $data['shipping_address']['state'] ?? null,
                    'postal_code' => $data['shipping_address']['postal_code'] ?? null,
                    'country' => $data['shipping_address']['country'] ?? null,
                    'latitude' => $data['shipping_address']['latitude'] ?? null,
                    'longitude' => $data['shipping_address']['longitude'] ?? null,
                    'delivery_instructions' => $data['shipping_address']['delivery_instructions'] ?? null,
                    'is_default' => true,
                    'status' => 'active',
                ]);
            }

            event(new CustomerCreated($customer));

            return $customer;
        });
    }
}
