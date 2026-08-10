<?php

namespace App\Http\Controllers\Inertia;

use App\Http\Controllers\Controller;
use App\Models\Products\ProductBrand;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductBrandController extends Controller
{
    public function index(Request $request): Response
    {
        $brands = ProductBrand::where('company_id', $request->user()->company_id)
            ->withCount('products')
            ->latest()
            ->paginate($request->get('per_page', 15));

        return Inertia::render('Products/Brands', [
            'brands' => $brands,
        ]);
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|string|max:500',
            'status' => 'nullable|string',
        ]);

        $validated['company_id'] = $request->user()->company_id;
        $validated['slug'] = \Str::slug($validated['name']);

        ProductBrand::create($validated);

        return redirect()->route('product-brands.index')->with('success', 'Brand created successfully');
    }

    public function update(Request $request, ProductBrand $productBrand): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|string|max:500',
            'status' => 'nullable|string',
        ]);

        if (isset($validated['name'])) {
            $validated['slug'] = \Str::slug($validated['name']);
        }

        $productBrand->update($validated);

        return redirect()->route('product-brands.index')->with('success', 'Brand updated successfully');
    }

    public function destroy(Request $request, ProductBrand $productBrand): \Illuminate\Http\RedirectResponse
    {
        $productBrand->delete();

        return redirect()->route('product-brands.index')->with('success', 'Brand deleted successfully');
    }
}
