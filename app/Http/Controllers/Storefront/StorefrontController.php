<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Products\Product;
use App\Models\Products\ProductCategory;
use App\Models\Products\ProductBrand;
use App\Models\Products\FeaturedProduct;
use App\Models\Wishlists\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class StorefrontController extends Controller
{
    public function index(Request $request)
    {
        $company = $this->getCompany();

        $categories = ProductCategory::where('company_id', $company->id)->where('status', 'active')->withCount('products')->get();
        $brands = ProductBrand::where('company_id', $company->id)->where('status', 'active')->get();

        $featured = FeaturedProduct::where('company_id', $company->id)
            ->active()
            ->with('product.category', 'product.brand', 'product.unit')
            ->orderBy('sort_order')
            ->limit(8)
            ->get()
            ->pluck('product')
            ->filter();

        // Landing page stats - these can be made dynamic via settings/CMS later
        $stats = [
            'retailers' => '10,000+',
            'categories' => '500+',
            'states' => '36',
            'delivery_rate' => '99%',
        ];

        // Testimonials - these can be made dynamic via CMS later
        $testimonials = [
            [
                'name' => 'Chinedu A.',
                'role' => 'Retail Store Owner, Lagos',
                'quote' => 'Supply 4 Me has made it easier for us to stock genuine products at great prices. Delivery is always on time.',
            ],
            [
                'name' => 'Fatima R.',
                'role' => 'Mini Mart, Kano',
                'quote' => 'A reliable partner for our business. The variety and pricing help us serve our customers better.',
            ],
            [
                'name' => 'Tunde M.',
                'role' => 'Distributor, Abuja',
                'quote' => 'Professional service and genuine products. Highly recommended for any growing business.',
            ],
        ];

        return Inertia::render('Storefront/Home', [
            'categories' => $categories,
            'brands' => $brands,
            'featured' => $featured,
            'cartCount' => $this->getCartCount(),
            'company' => $company,
            'stats' => $stats,
            'testimonials' => $testimonials,
        ]);
    }

    public function products(Request $request)
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

        $categories = ProductCategory::where('company_id', $company->id)->where('status', 'active')->withCount('products')->get();
        $brands = ProductBrand::where('company_id', $company->id)->where('status', 'active')->get();

        return Inertia::render('Storefront/Products', [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'filters' => $request->only(['search', 'category_id', 'brand_id', 'sort']),
            'cartCount' => $this->getCartCount(),
            'wishlistIds' => $this->getWishlistIds(),
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
        $wishlistIds = $this->getWishlistIds();

        return Inertia::render('Storefront/ProductDetail', [
            'product' => $product,
            'cartCount' => $cartCount,
            'wishlistIds' => $wishlistIds,
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

    protected function getWishlistIds(): array
    {
        $customer = Auth::guard('customer')->user();
        if (!$customer) return [];

        return Wishlist::where('customer_id', $customer->id)
            ->pluck('product_id')
            ->toArray();
    }
}
