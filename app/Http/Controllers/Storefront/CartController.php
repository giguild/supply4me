<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Products\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        $items = [];
        $subtotal = 0;

        foreach ($cart as $key => $item) {
            $product = Product::with(['category', 'brand', 'unit'])
                ->where('id', $item['product_id'])
                ->first();

            if ($product) {
                $lineTotal = $product->selling_price * $item['quantity'];
                $items[] = [
                    'key' => $key,
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'price' => $product->selling_price,
                    'quantity' => $item['quantity'],
                    'line_total' => $lineTotal,
                    'category' => $product->category,
                    'brand' => $product->brand,
                    'unit' => $product->unit,
                    'minimum_order_quantity' => $product->minimum_order_quantity ?? 1,
                    'maximum_order_quantity' => $product->maximum_order_quantity,
                ];
                $subtotal += $lineTotal;
            }
        }

        $taxRate = 7.5;
        $taxAmount = $subtotal * ($taxRate / 100);
        $total = $subtotal + $taxAmount;

        return Inertia::render('Storefront/Cart', [
            'items' => $items,
            'subtotal' => $subtotal,
            'taxRate' => $taxRate,
            'taxAmount' => $taxAmount,
            'total' => $total,
            'cartCount' => array_sum(array_column($cart, 'quantity')),
        ]);
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|string',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::where('id', $request->product_id)
            ->where('status', 'active')
            ->where('is_sellable', true)
            ->firstOrFail();

        $minQty = $product->minimum_order_quantity ?? 1;
        $maxQty = $product->maximum_order_quantity;

        if ($request->quantity < $minQty) {
            return back()->withErrors(['quantity' => "Minimum order quantity is {$minQty}."]);
        }

        if ($maxQty && $request->quantity > $maxQty) {
            return back()->withErrors(['quantity' => "Maximum order quantity is {$maxQty}."]);
        }

        $cart = session()->get('cart', []);
        $key = $request->product_id;

        if (isset($cart[$key])) {
            $newQty = $cart[$key]['quantity'] + $request->quantity;
            if ($maxQty && $newQty > $maxQty) {
                return back()->withErrors(['quantity' => "Adding this quantity would exceed the maximum of {$maxQty}."]);
            }
            $cart[$key]['quantity'] = $newQty;
        } else {
            $cart[$key] = [
                'product_id' => $product->id,
                'quantity' => $request->quantity,
            ];
        }

        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Product added to cart');
    }

    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required|string',
            'quantity' => 'required|integer|min:0',
        ]);

        if ($request->quantity > 0) {
            $product = Product::where('id', $request->product_id)->first();
            if ($product) {
                $minQty = $product->minimum_order_quantity ?? 1;
                $maxQty = $product->maximum_order_quantity;

                if ($request->quantity < $minQty) {
                    return back()->withErrors(['quantity' => "Minimum order quantity is {$minQty}."]);
                }
                if ($maxQty && $request->quantity > $maxQty) {
                    return back()->withErrors(['quantity' => "Maximum order quantity is {$maxQty}."]);
                }
            }
        }

        $cart = session()->get('cart', []);
        $key = $request->product_id;

        if ($request->quantity === 0) {
            unset($cart[$key]);
        } elseif (isset($cart[$key])) {
            $cart[$key]['quantity'] = $request->quantity;
        }

        session()->put('cart', $cart);

        return redirect()->route('storefront.cart');
    }

    public function remove(Request $request)
    {
        $cart = session()->get('cart', []);
        unset($cart[$request->product_id]);
        session()->put('cart', $cart);

        return redirect()->route('storefront.cart')->with('success', 'Item removed from cart');
    }

    public function count()
    {
        $cart = session()->get('cart', []);
        $count = array_sum(array_column($cart, 'quantity'));

        return response()->json(['count' => $count]);
    }
}
