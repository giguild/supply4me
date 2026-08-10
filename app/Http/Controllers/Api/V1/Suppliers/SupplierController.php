<?php

namespace App\Http\Controllers\Api\V1\Suppliers;

use App\Http\Controllers\Controller;
use App\Models\Suppliers\Supplier;
use App\Resources\Suppliers\SupplierResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $suppliers = Supplier::query()
            ->withCount('products')
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%")->orWhere('code', 'like', "%{$s}%"))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->paginate($request->get('per_page', 15));

        return $this->paginated($suppliers, SupplierResource::collection($suppliers->items()));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:suppliers,code',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'tax_number' => 'nullable|string|max:50',
            'payment_terms' => 'nullable|string|max:100',
            'lead_time_days' => 'nullable|integer|min:0',
            'minimum_order_value' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'contact_person' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
        ]);

        $supplier = Supplier::create($validated);

        return $this->created(
            new SupplierResource($supplier),
            'Supplier created successfully'
        );
    }

    public function show(Supplier $supplier): JsonResponse
    {
        return $this->success(
            new SupplierResource($supplier->load('products'))
        );
    }

    public function update(Request $request, Supplier $supplier): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'code' => 'sometimes|string|max:50|unique:suppliers,code,' . $supplier->id,
            'email' => 'sometimes|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'tax_number' => 'nullable|string|max:50',
            'payment_terms' => 'nullable|string|max:100',
            'lead_time_days' => 'nullable|integer|min:0',
            'minimum_order_value' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'contact_person' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'status' => 'sometimes|string|in:active,inactive,suspended',
        ]);

        $supplier->update($validated);

        return $this->success(
            new SupplierResource($supplier->fresh()),
            'Supplier updated successfully'
        );
    }

    public function destroy(Supplier $supplier): JsonResponse
    {
        $supplier->delete();

        return $this->noContent('Supplier deleted successfully');
    }

    public function products(Supplier $supplier, Request $request): JsonResponse
    {
        $products = $supplier->products()
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => 'Supplier products retrieved successfully',
            'data' => $products,
        ]);
    }

    public function attachProduct(Request $request, Supplier $supplier): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'cost_price' => 'required|numeric|min:0',
            'lead_time_days' => 'nullable|integer|min:0',
            'minimum_order_quantity' => 'nullable|integer|min:1',
            'is_preferred' => 'sometimes|boolean',
        ]);

        $supplier->products()->syncWithoutDetaching([
            $validated['product_id'] => [
                'cost_price' => $validated['cost_price'],
                'lead_time_days' => $validated['lead_time_days'] ?? null,
                'minimum_order_quantity' => $validated['minimum_order_quantity'] ?? 1,
                'is_preferred' => $validated['is_preferred'] ?? false,
            ],
        ]);

        return $this->success(message: 'Product attached to supplier successfully');
    }

    public function detachProduct(Supplier $supplier, int $productId): JsonResponse
    {
        $supplier->products()->detach($productId);

        return $this->noContent('Product detached from supplier successfully');
    }
}
