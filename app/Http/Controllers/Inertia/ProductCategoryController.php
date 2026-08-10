<?php

namespace App\Http\Controllers\Inertia;

use App\Http\Controllers\Controller;
use App\Models\Products\ProductCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductCategoryController extends Controller
{
    public function index(Request $request): Response
    {
        $categories = ProductCategory::where('company_id', $request->user()->company_id)
            ->with('parent')
            ->withCount('products')
            ->latest()
            ->paginate($request->get('per_page', 15));

        $allCategories = ProductCategory::where('company_id', $request->user()->company_id)
            ->whereNull('parent_id')
            ->get();

        return Inertia::render('Products/Categories', [
            'categories' => $categories,
            'parentCategories' => $allCategories,
        ]);
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:product_categories,id',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'nullable|string',
        ]);

        $validated['company_id'] = $request->user()->company_id;
        $validated['slug'] = \Str::slug($validated['name']);

        ProductCategory::create($validated);

        return redirect()->route('product-categories.index')->with('success', 'Category created successfully');
    }

    public function update(Request $request, ProductCategory $productCategory): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:product_categories,id',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'nullable|string',
        ]);

        if (isset($validated['name'])) {
            $validated['slug'] = \Str::slug($validated['name']);
        }

        $productCategory->update($validated);

        return redirect()->route('product-categories.index')->with('success', 'Category updated successfully');
    }

    public function destroy(Request $request, ProductCategory $productCategory): \Illuminate\Http\RedirectResponse
    {
        $productCategory->delete();

        return redirect()->route('product-categories.index')->with('success', 'Category deleted successfully');
    }
}
