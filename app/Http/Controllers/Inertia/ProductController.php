<?php

namespace App\Http\Controllers\Inertia;

use App\Http\Controllers\Controller;
use App\Models\Products\Product;
use App\Models\Products\ProductBrand;
use App\Models\Products\ProductCategory;
use App\Models\Products\ProductUnit;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Product::where('company_id', $request->user()->company_id)
            ->with(['category', 'brand', 'unit']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $products = $query->latest()->paginate($request->get('per_page', 15));

        $categories = ProductCategory::where('company_id', $request->user()->company_id)->get();
        $brands = ProductBrand::where('company_id', $request->user()->company_id)->get();

        return Inertia::render('Products/Index', [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'filters' => $request->only(['search', 'category_id', 'brand_id', 'status']),
        ]);
    }

    public function create(Request $request): Response
    {
        $categories = ProductCategory::where('company_id', $request->user()->company_id)->get();
        $brands = ProductBrand::where('company_id', $request->user()->company_id)->get();
        $units = ProductUnit::where('company_id', $request->user()->company_id)->get();

        return Inertia::render('Products/Create', [
            'categories' => $categories,
            'brands' => $brands,
            'units' => $units,
        ]);
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100',
            'barcode' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'category_id' => 'nullable|exists:product_categories,id',
            'brand_id' => 'nullable|exists:product_brands,id',
            'unit_id' => 'nullable|exists:product_units,id',
            'product_type' => 'nullable|string|max:50',
            'is_sellable' => 'nullable|boolean',
            'is_purchasable' => 'nullable|boolean',
            'is_stockable' => 'nullable|boolean',
            'weight' => 'nullable|numeric|min:0',
            'weight_unit' => 'nullable|string|max:10',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'minimum_price' => 'nullable|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'reorder_level' => 'nullable|integer|min:0',
            'reorder_quantity' => 'nullable|integer|min:0',
            'minimum_order_quantity' => 'nullable|integer|min:0',
            'status' => 'nullable|string',
            'is_featured' => 'nullable|boolean',
            'tags' => 'nullable|array',
            'attributes' => 'nullable|array',
        ]);

        $validated['company_id'] = $request->user()->company_id;

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Product created successfully');
    }

    public function show(Request $request, Product $product): Response
    {
        $product->load([
            'category',
            'brand',
            'unit',
            'stockItems' => fn ($q) => $q->with('warehouse'),
            'variants',
        ]);

        return Inertia::render('Products/Show', [
            'product' => $product,
        ]);
    }

    public function edit(Request $request, Product $product): Response
    {
        $categories = ProductCategory::where('company_id', $request->user()->company_id)->get();
        $brands = ProductBrand::where('company_id', $request->user()->company_id)->get();
        $units = ProductUnit::where('company_id', $request->user()->company_id)->get();

        return Inertia::render('Products/Edit', [
            'product' => $product,
            'categories' => $categories,
            'brands' => $brands,
            'units' => $units,
        ]);
    }

    public function update(Request $request, Product $product): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100',
            'barcode' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'category_id' => 'nullable|exists:product_categories,id',
            'brand_id' => 'nullable|exists:product_brands,id',
            'unit_id' => 'nullable|exists:product_units,id',
            'product_type' => 'nullable|string|max:50',
            'is_sellable' => 'nullable|boolean',
            'is_purchasable' => 'nullable|boolean',
            'is_stockable' => 'nullable|boolean',
            'weight' => 'nullable|numeric|min:0',
            'weight_unit' => 'nullable|string|max:10',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'minimum_price' => 'nullable|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'reorder_level' => 'nullable|integer|min:0',
            'reorder_quantity' => 'nullable|integer|min:0',
            'minimum_order_quantity' => 'nullable|integer|min:0',
            'status' => 'nullable|string',
            'is_featured' => 'nullable|boolean',
            'tags' => 'nullable|array',
            'attributes' => 'nullable|array',
        ]);

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Product updated successfully');
    }

    public function destroy(Request $request, Product $product): \Illuminate\Http\RedirectResponse
    {
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully');
    }
}
