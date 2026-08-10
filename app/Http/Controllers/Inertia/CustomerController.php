<?php

namespace App\Http\Controllers\Inertia;

use App\Http\Controllers\Controller;
use App\Models\Branches\Branch;
use App\Models\Customers\Customer;
use App\Models\Core\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CustomerController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Customer::where('company_id', $request->user()->company_id)
            ->with('assignedTo');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('customer_number', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('customer_type')) {
            $query->where('customer_type', $request->customer_type);
        }

        $customers = $query->latest()->paginate($request->get('per_page', 15));

        return Inertia::render('Customers/Index', [
            'customers' => $customers,
            'filters' => $request->only(['search', 'status', 'customer_type']),
        ]);
    }

    public function create(Request $request): Response
    {
        $branches = Branch::where('company_id', $request->user()->company_id)->get();
        $users = User::where('company_id', $request->user()->company_id)->get();

        return Inertia::render('Customers/Create', [
            'branches' => $branches,
            'users' => $users,
        ]);
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'trade_name' => 'nullable|string|max:255',
            'customer_type' => 'nullable|string|max:50',
            'tax_number' => 'nullable|string|max:50',
            'registration_number' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'mobile' => 'nullable|string|max:50',
            'fax' => 'nullable|string|max:50',
            'website' => 'nullable|url|max:255',
            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'credit_limit' => 'nullable|numeric|min:0',
            'payment_terms_days' => 'nullable|integer|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['company_id'] = $request->user()->company_id;

        Customer::create($validated);

        return redirect()->route('customers.index')->with('success', 'Customer created successfully');
    }

    public function show(Request $request, Customer $customer): Response
    {
        $customer->load([
            'contacts',
            'shippingAddresses',
            'orders' => fn ($q) => $q->latest()->limit(10),
            'invoices' => fn ($q) => $q->latest()->limit(10),
            'notes',
            'assignedTo',
        ]);

        return Inertia::render('Customers/Show', [
            'customer' => $customer,
        ]);
    }

    public function edit(Request $request, Customer $customer): Response
    {
        $branches = Branch::where('company_id', $request->user()->company_id)->get();
        $users = User::where('company_id', $request->user()->company_id)->get();

        return Inertia::render('Customers/Edit', [
            'customer' => $customer,
            'branches' => $branches,
            'users' => $users,
        ]);
    }

    public function update(Request $request, Customer $customer): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'trade_name' => 'nullable|string|max:255',
            'customer_type' => 'nullable|string|max:50',
            'tax_number' => 'nullable|string|max:50',
            'registration_number' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'mobile' => 'nullable|string|max:50',
            'fax' => 'nullable|string|max:50',
            'website' => 'nullable|url|max:255',
            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'credit_limit' => 'nullable|numeric|min:0',
            'payment_terms_days' => 'nullable|integer|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $customer->update($validated);

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully');
    }

    public function destroy(Request $request, Customer $customer): \Illuminate\Http\RedirectResponse
    {
        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully');
    }

    public function storeContact(Request $request, Customer $customer): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'position' => 'nullable|string|max:100',
            'is_primary' => 'nullable|boolean',
        ]);

        $customer->contacts()->create($validated);

        return redirect()->route('customers.show', $customer)->with('success', 'Contact added successfully');
    }

    public function updateContact(Request $request, Customer $customer, $contact): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'position' => 'nullable|string|max:100',
            'is_primary' => 'nullable|boolean',
        ]);

        $customer->contacts()->where('id', $contact)->update($validated);

        return redirect()->route('customers.show', $customer)->with('success', 'Contact updated successfully');
    }

    public function destroyContact(Request $request, Customer $customer, $contact): \Illuminate\Http\RedirectResponse
    {
        $customer->contacts()->where('id', $contact)->delete();

        return redirect()->route('customers.show', $customer)->with('success', 'Contact deleted successfully');
    }
}
