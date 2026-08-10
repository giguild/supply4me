<?php

namespace App\Http\Requests\Customers;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $customerId = $this->route('customer')?->id;

        return [
            'name' => 'sometimes|string|max:255',
            'code' => 'sometimes|string|max:50|unique:customers,code,' . $customerId,
            'email' => 'sometimes|email|max:255',
            'phone' => 'nullable|string|max:50',
            'mobile' => 'nullable|string|max:50',
            'type' => 'sometimes|string|in:retailer,wholesaler,distributor,institution,government',
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
            'status' => 'sometimes|string|in:active,inactive,suspended',
            'credit_status' => 'sometimes|string|in:active,suspended,blocked',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Customer name is required',
            'email.email' => 'Please provide a valid email address',
            'type.in' => 'Invalid customer type',
            'status.in' => 'Invalid status',
            'credit_status.in' => 'Invalid credit status',
        ];
    }
}
