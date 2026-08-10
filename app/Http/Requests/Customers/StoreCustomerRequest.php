<?php

namespace App\Http\Requests\Customers;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:customers,code',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'mobile' => 'nullable|string|max:50',
            'type' => 'required|string|in:retailer,wholesaler,distributor,institution,government',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'tax_number' => 'nullable|string|max:50',
            'credit_limit' => 'nullable|numeric|min:0',
            'payment_terms' => 'nullable|string|max:100',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'price_list_id' => 'nullable|exists:price_lists,id',
            'sales_rep_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string|max:1000',
            'contacts' => 'nullable|array',
            'contacts.*.name' => 'required|string|max:255',
            'contacts.*.email' => 'nullable|email|max:255',
            'contacts.*.phone' => 'nullable|string|max:50',
            'contacts.*.position' => 'nullable|string|max:100',
            'contacts.*.is_primary' => 'sometimes|boolean',
            'addresses' => 'nullable|array',
            'addresses.*.label' => 'nullable|string|max:100',
            'addresses.*.address_line_1' => 'required|string|max:255',
            'addresses.*.address_line_2' => 'nullable|string|max:255',
            'addresses.*.city' => 'required|string|max:100',
            'addresses.*.state' => 'required|string|max:100',
            'addresses.*.country' => 'required|string|max:100',
            'addresses.*.postal_code' => 'required|string|max:20',
            'addresses.*.is_default' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Customer name is required',
            'code.required' => 'Customer code is required',
            'code.unique' => 'This customer code already exists',
            'email.required' => 'Email address is required',
            'email.email' => 'Please provide a valid email address',
            'type.required' => 'Customer type is required',
            'type.in' => 'Invalid customer type',
        ];
    }
}
