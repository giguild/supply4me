<?php

namespace App\Http\Controllers\Inertia;

use App\Http\Controllers\Controller;
use App\Models\Products\FeaturedProduct;
use App\Models\Products\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FeaturedProductController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->user()->company_id;

        $featured = FeaturedProduct::where('company_id', $companyId)
            ->with('product')
            ->orderBy('sort_order')
            ->get();

        $products = Product::where('company_id', $companyId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'sku', 'selling_price']);

        return Inertia::render('FeaturedProducts/Index', [
            'featured' => $featured,
            'products' => $products,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $companyId = $request->user()->company_id;

        $count = FeaturedProduct::where('company_id', $companyId)->count();
        if ($count >= 8) {
            return back()->withErrors(['product_id' => 'Maximum 8 featured products allowed.']);
        }

        $nextSort = FeaturedProduct::where('company_id', $companyId)->max('sort_order') + 1;

        FeaturedProduct::updateOrCreate(
            ['company_id' => $companyId, 'product_id' => $validated['product_id']],
            ['sort_order' => $nextSort, 'is_active' => true]
        );

        return redirect()->route('featured-products.index')->with('success', 'Product added to featured.');
    }

    public function update(Request $request, FeaturedProduct $featuredProduct)
    {
        $validated = $request->validate([
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $featuredProduct->update($validated);

        return redirect()->route('featured-products.index')->with('success', 'Featured product updated.');
    }

    public function destroy(FeaturedProduct $featuredProduct)
    {
        $featuredProduct->delete();

        return redirect()->route('featured-products.index')->with('success', 'Product removed from featured.');
    }
}