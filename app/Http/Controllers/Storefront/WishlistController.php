<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Wishlists\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class WishlistController extends Controller
{
    public function index()
    {
        $customer = Auth::guard('customer')->user();

        $items = Wishlist::where('customer_id', $customer->id)
            ->with(['product.category', 'product.brand', 'product.unit'])
            ->latest()
            ->get();

        return Inertia::render('Storefront/Wishlist', [
            'items' => $items,
            'cartCount' => $this->getCartCount(),
        ]);
    }

    public function toggle(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $request->validate([
            'product_id' => 'required|string|exists:products,id',
        ]);

        $existing = Wishlist::where('customer_id', $customer->id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['added' => false]);
        }

        Wishlist::create([
            'customer_id' => $customer->id,
            'product_id' => $request->product_id,
        ]);

        return response()->json(['added' => true]);
    }

    public function destroy(string $id)
    {
        $customer = Auth::guard('customer')->user();

        Wishlist::where('customer_id', $customer->id)
            ->where('id', $id)
            ->firstOrFail()
            ->delete();

        return redirect()->route('storefront.wishlist')->with('success', 'Removed from wishlist');
    }

    protected function getCartCount(): int
    {
        $cart = session()->get('cart', []);
        return array_sum(array_column($cart, 'quantity'));
    }
}
