<?php

namespace App\Http\Controllers\Api\V1\Customers;

use App\Http\Controllers\Controller;
use App\Models\Customers\Customer;
use App\Models\Customers\CustomerContact;
use App\Resources\Customers\CustomerContactResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerContactController extends Controller
{
    public function index(Customer $customer): JsonResponse
    {
        $contacts = $customer->contacts()->paginate(15);

        return response()->json([
            'success' => true,
            'message' => 'Contacts retrieved successfully',
            'data' => CustomerContactResource::collection($contacts),
            'meta' => [
                'current_page' => $contacts->currentPage(),
                'last_page' => $contacts->lastPage(),
                'per_page' => $contacts->perPage(),
                'total' => $contacts->total(),
            ],
        ]);
    }

    public function store(Request $request, Customer $customer): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'mobile' => 'nullable|string|max:50',
            'position' => 'nullable|string|max:100',
            'is_primary' => 'sometimes|boolean',
            'notes' => 'nullable|string|max:500',
        ]);

        $validated['customer_id'] = $customer->id;

        if ($validated['is_primary'] ?? false) {
            $customer->contacts()->where('is_primary', true)->update(['is_primary' => false]);
        }

        $contact = CustomerContact::create($validated);

        return $this->created(
            new CustomerContactResource($contact),
            'Contact created successfully'
        );
    }

    public function show(Customer $customer, CustomerContact $contact): JsonResponse
    {
        $this->authorizeContact($customer, $contact);

        return $this->success(new CustomerContactResource($contact));
    }

    public function update(Request $request, Customer $customer, CustomerContact $contact): JsonResponse
    {
        $this->authorizeContact($customer, $contact);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'mobile' => 'nullable|string|max:50',
            'position' => 'nullable|string|max:100',
            'is_primary' => 'sometimes|boolean',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validated['is_primary'] ?? false) {
            $customer->contacts()->where('is_primary', true)->update(['is_primary' => false]);
        }

        $contact->update($validated);

        return $this->success(
            new CustomerContactResource($contact->fresh()),
            'Contact updated successfully'
        );
    }

    public function destroy(Customer $customer, CustomerContact $contact): JsonResponse
    {
        $this->authorizeContact($customer, $contact);

        $contact->delete();

        return $this->noContent('Contact deleted successfully');
    }

    protected function authorizeContact(Customer $customer, CustomerContact $contact): void
    {
        if ($contact->customer_id !== $customer->id) {
            abort(403, 'Contact does not belong to this customer');
        }
    }
}
