<?php

namespace App\Http\Controllers\Api\V1\Products;

use App\Http\Controllers\Controller;
use App\Models\Products\ProductCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $categories = ProductCategory::query()
            ->withCount('products')
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->when($request->parent_id, fn ($q, $p) => $q->where('parent_id', $p))
            ->orderBy('sort_order')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => 'Categories retrieved successfully',
            'data' => $categories,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'parent_id' => 'nullable|exists:product_categories,id',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'sometimes|boolean',
        ]);

        $category = ProductCategory::create($validated);

        return $this->created($category, 'Category created successfully');
    }

    public function show(ProductCategory $productCategory): JsonResponse
    {
        return $this->success(
            $productCategory->load(['products', 'children', 'parent'])
        );
    }

    public function update(Request $request, ProductCategory $productCategory): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:500',
            'parent_id' => 'nullable|exists:product_categories,id',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'sometimes|boolean',
        ]);

        $productCategory->update($validated);

        return $this->success(
            $productCategory->fresh(),
            'Category updated successfully'
        );
    }

    public function destroy(ProductCategory $productCategory): JsonResponse
    {
        if ($productCategory->products()->exists() || $productCategory->children()->exists()) {
            return $this->error('Cannot delete category with products or subcategories', 422);
        }

        $productCategory->delete();

        return $this->noContent('Category deleted successfully');
    }
}
