<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Customers\CustomerContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerContactController extends Controller
{
    public function store(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'mobile' => 'nullable|string|max:50',
            'is_primary' => 'nullable|boolean',
        ]);

        $validated['customer_id'] = $customer->id;
        $validated['status'] = 'active';

        if (!empty($validated['is_primary']) && $validated['is_primary']) {
            CustomerContact::where('customer_id', $customer->id)
                ->where('is_primary', true)
                ->update(['is_primary' => false]);
        }

        CustomerContact::create($validated);

        return back()->with('success', 'Contact added successfully');
    }

    public function update(Request $request, CustomerContact $contact)
    {
        $customer = Auth::guard('customer')->user();

        if ($contact->customer_id !== $customer->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'mobile' => 'nullable|string|max:50',
            'is_primary' => 'nullable|boolean',
        ]);

        if (!empty($validated['is_primary']) && $validated['is_primary']) {
            CustomerContact::where('customer_id', $customer->id)
                ->where('is_primary', true)
                ->where('id', '!=', $contact->id)
                ->update(['is_primary' => false]);
        }

        $contact->update($validated);

        return back()->with('success', 'Contact updated successfully');
    }

    public function destroy(CustomerContact $contact)
    {
        $customer = Auth::guard('customer')->user();

        if ($contact->customer_id !== $customer->id) {
            abort(403);
        }

        $contact->delete();

        return back()->with('success', 'Contact deleted successfully');
    }
}
