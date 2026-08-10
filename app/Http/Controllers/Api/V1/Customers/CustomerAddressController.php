<?php

namespace App\Http\Controllers\Api\V1\Customers;

use App\Http\Controllers\Controller;
use App\Models\Customers\Customer;
use App\Models\Customers\CustomerShippingAddress;
use App\Resources\Customers\CustomerAddressResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerAddressController extends Controller
{
    public function index(Customer $customer): JsonResponse
    {
        $addresses = $customer->addresses()->paginate(15);

        return response()->json([
            'success' => true,
            'message' => 'Addresses retrieved successfully',
            'data' => CustomerAddressResource::collection($addresses),
            'meta' => [
                'current_page' => $addresses->currentPage(),
                'last_page' => $addresses->lastPage(),
                'per_page' => $addresses->perPage(),
                'total' => $addresses->total(),
            ],
        ]);
    }

    public function store(Request $request, Customer $customer): JsonResponse
    {
        $validated = $request->validate([
            'label' => 'sometimes|string|max:100',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'phone' => 'nullable|string|max:50',
            'is_default' => 'sometimes|boolean',
            'delivery_instructions' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $validated['customer_id'] = $customer->id;

        if ($validated['is_default'] ?? false) {
            $customer->addresses()->where('is_default', true)->update(['is_default' => false]);
        }

        $address = CustomerShippingAddress::create($validated);

        return $this->created(
            new CustomerAddressResource($address),
            'Address created successfully'
        );
    }

    public function show(Customer $customer, CustomerShippingAddress $address): JsonResponse
    {
        $this->authorizeAddress($customer, $address);

        return $this->success(new CustomerAddressResource($address));
    }

    public function update(Request $request, Customer $customer, CustomerShippingAddress $address): JsonResponse
    {
        $this->authorizeAddress($customer, $address);

        $validated = $request->validate([
            'label' => 'sometimes|string|max:100',
            'address_line_1' => 'sometimes|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'sometimes|string|max:100',
            'state' => 'sometimes|string|max:100',
            'country' => 'sometimes|string|max:100',
            'postal_code' => 'sometimes|string|max:20',
            'phone' => 'nullable|string|max:50',
            'is_default' => 'sometimes|boolean',
            'delivery_instructions' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        if ($validated['is_default'] ?? false) {
            $customer->addresses()->where('is_default', true)->update(['is_default' => false]);
        }

        $address->update($validated);

        return $this->success(
            new CustomerAddressResource($address->fresh()),
            'Address updated successfully'
        );
    }

    public function destroy(Customer $customer, CustomerShippingAddress $address): JsonResponse
    {
        $this->authorizeAddress($customer, $address);

        $address->delete();

        return $this->noContent('Address deleted successfully');
    }

    protected function authorizeAddress(Customer $customer, CustomerShippingAddress $address): void
    {
        if ($address->customer_id !== $customer->id) {
            abort(403, 'Address does not belong to this customer');
        }
    }
}
