<?php

namespace App\Actions\Suppliers;

use App\Models\Suppliers\Supplier;

class CreateSupplierAction
{
    public function execute(array $data): Supplier
    {
        return Supplier::create([
            'company_id' => $data['company_id'],
            'name' => $data['name'],
            'contact_person' => $data['contact_person'] ?? null,
            'tax_number' => $data['tax_number'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'mobile' => $data['mobile'] ?? null,
            'website' => $data['website'] ?? null,
            'address_line_1' => $data['address_line_1'] ?? null,
            'address_line_2' => $data['address_line_2'] ?? null,
            'city' => $data['city'] ?? null,
            'state' => $data['state'] ?? null,
            'postal_code' => $data['postal_code'] ?? null,
            'country' => $data['country'] ?? null,
            'payment_terms_days' => $data['payment_terms_days'] ?? 30,
            'lead_time_days' => $data['lead_time_days'] ?? null,
            'minimum_order_amount' => $data['minimum_order_amount'] ?? null,
            'rating' => $data['rating'] ?? null,
            'bank_name' => $data['bank_name'] ?? null,
            'bank_account_name' => $data['bank_account_name'] ?? null,
            'bank_account_number' => $data['bank_account_number'] ?? null,
            'bank_routing_number' => $data['bank_routing_number'] ?? null,
            'bank_swift_code' => $data['bank_swift_code'] ?? null,
            'status' => $data['status'] ?? 'active',
            'notes' => $data['notes'] ?? null,
            'metadata' => $data['metadata'] ?? [],
        ]);
    }
}
