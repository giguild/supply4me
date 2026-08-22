<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Customers\CustomerShippingAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ShippingAddressController extends Controller
{
    public function store(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:2',
            'delivery_instructions' => 'nullable|string|max:500',
            'is_default' => 'nullable|boolean',
        ]);

        $validated['customer_id'] = $customer->id;
        $validated['status'] = 'active';

        if (!empty($validated['is_default']) && $validated['is_default']) {
            CustomerShippingAddress::where('customer_id', $customer->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        CustomerShippingAddress::create($validated);

        return back()->with('success', 'Shipping address added successfully');
    }

    public function update(Request $request, CustomerShippingAddress $address)
    {
        $customer = Auth::guard('customer')->user();

        if ($address->customer_id !== $customer->id) {
            abort(403);
        }

        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:2',
            'delivery_instructions' => 'nullable|string|max:500',
            'is_default' => 'nullable|boolean',
        ]);

        if (!empty($validated['is_default']) && $validated['is_default']) {
            CustomerShippingAddress::where('customer_id', $customer->id)
                ->where('is_default', true)
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);
        }

        $address->update($validated);

        return back()->with('success', 'Shipping address updated successfully');
    }

    public function destroy(CustomerShippingAddress $address)
    {
        $customer = Auth::guard('customer')->user();

        if ($address->customer_id !== $customer->id) {
            abort(403);
        }

        $address->delete();

        return back()->with('success', 'Shipping address deleted successfully');
    }
}
