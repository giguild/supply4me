<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Products\Product;
use App\Models\Products\ProductCategory;
use App\Models\Products\ProductBrand;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StorefrontController extends Controller
{
    public function index(Request $request)
    {
        $company = $this->getCompany();

        $query = Product::where('company_id', $company->id)
            ->where('status', 'active')
            ->where('is_sellable', true)
            ->with(['category', 'brand', 'unit']);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%")
                  ->orWhere('sku', 'like', "%{$request->search}%");
            });
        }

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->brand_id) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->sort === 'price_asc') {
            $query->orderBy('selling_price', 'asc');
        } elseif ($request->sort === 'price_desc') {
            $query->orderBy('selling_price', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = ProductCategory::where('company_id', $company->id)->where('status', 'active')->get();
        $brands = ProductBrand::where('company_id', $company->id)->where('status', 'active')->get();

        $cartCount = $this->getCartCount();

        return Inertia::render('Storefront/Home', [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'filters' => $request->only(['search', 'category_id', 'brand_id', 'sort']),
            'cartCount' => $cartCount,
            'company' => $company,
        ]);
    }

    public function show(Request $request, string $slug)
    {
        $company = $this->getCompany();

        $product = Product::where('company_id', $company->id)
            ->where('status', 'active')
            ->where('is_sellable', true)
            ->where('slug', $slug)
            ->with(['category', 'brand', 'unit', 'variants', 'stockItems.warehouse'])
            ->firstOrFail();

        $cartCount = $this->getCartCount();

        return Inertia::render('Storefront/ProductDetail', [
            'product' => $product,
            'cartCount' => $cartCount,
            'company' => $company,
        ]);
    }

    protected function getCompany()
    {
        return \App\Models\Companies\Company::firstOrFail();
    }

    protected function getCartCount(): int
    {
        $cart = session()->get('cart', []);
        return array_sum(array_column($cart, 'quantity'));
    }
}
