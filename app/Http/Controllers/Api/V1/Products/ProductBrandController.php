<?php

namespace App\Http\Controllers\Api\V1\Products;

use App\Http\Controllers\Controller;
use App\Models\Products\ProductBrand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductBrandController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $brands = ProductBrand::query()
            ->withCount('products')
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => 'Brands retrieved successfully',
            'data' => $brands,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|max:2048',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('brands', 'public');
        }

        $brand = ProductBrand::create($validated);

        return $this->created($brand, 'Brand created successfully');
    }

    public function show(ProductBrand $productBrand): JsonResponse
    {
        return $this->success(
            $productBrand->load('products')
        );
    }

    public function update(Request $request, ProductBrand $productBrand): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:500',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|max:2048',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('brands', 'public');
        }

        $productBrand->update($validated);

        return $this->success(
            $productBrand->fresh(),
            'Brand updated successfully'
        );
    }

    public function destroy(ProductBrand $productBrand): JsonResponse
    {
        if ($productBrand->products()->exists()) {
            return $this->error('Cannot delete brand with products', 422);
        }

        $productBrand->delete();

        return $this->noContent('Brand deleted successfully');
    }
}
