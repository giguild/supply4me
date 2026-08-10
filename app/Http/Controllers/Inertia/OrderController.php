<?php

namespace App\Http\Controllers\Inertia;

use App\Http\Controllers\Controller;
use App\Models\Customers\Customer;
use App\Models\Orders\Order;
use App\Models\Orders\OrderItem;
use App\Models\Orders\OrderStatusHistory;
use App\Models\Products\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Order::where('company_id', $request->user()->company_id)
            ->with('customer');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn ($cq) => $cq->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('order_type')) {
            $query->where('order_type', $request->order_type);
        }

        $orders = $query->latest()->paginate($request->get('per_page', 15));

        return Inertia::render('Orders/Index', [
            'orders' => $orders,
            'filters' => $request->only(['search', 'status', 'order_type']),
        ]);
    }

    public function create(Request $request): Response
    {
        $customers = Customer::where('company_id', $request->user()->company_id)->get();
        $products = Product::where('company_id', $request->user()->company_id)
            ->where('is_sellable', true)
            ->get();

        return Inertia::render('Orders/Create', [
            'customers' => $customers,
            'products' => $products,
        ]);
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'order_type' => 'nullable|string|max:50',
            'priority' => 'nullable|string|max:50',
            'payment_terms_days' => 'nullable|integer|min:0',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'internal_notes' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        $companyId = $request->user()->company_id;

        $subtotal = 0;
        $taxAmount = 0;
        $discountAmount = 0;

        foreach ($validated['items'] as $item) {
            $lineTotal = $item['quantity'] * $item['unit_price'];
            $itemDiscount = $lineTotal * (($item['discount_percentage'] ?? 0) / 100);
            $lineTotal -= $itemDiscount;
            $subtotal += $lineTotal;
            $discountAmount += $itemDiscount;
        }

        $order = Order::create([
            'company_id' => $companyId,
            'customer_id' => $validated['customer_id'],
            'order_type' => $validated['order_type'] ?? null,
            'status' => 'draft',
            'payment_status' => 'unpaid',
            'fulfillment_status' => 'unfulfilled',
            'priority' => $validated['priority'] ?? 'normal',
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'shipping_amount' => 0,
            'total_amount' => $subtotal - $discountAmount + $taxAmount,
            'payment_terms_days' => $validated['payment_terms_days'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'internal_notes' => $validated['internal_notes'] ?? null,
            'assigned_to' => $validated['assigned_to'] ?? null,
        ]);

        foreach ($validated['items'] as $item) {
            $lineTotal = $item['quantity'] * $item['unit_price'];
            $itemDiscount = $lineTotal * (($item['discount_percentage'] ?? 0) / 100);
            $lineTotal -= $itemDiscount;

            $product = Product::find($item['product_id']);

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'sku' => $product->sku,
                'name' => $product->name,
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'discount_percentage' => $item['discount_percentage'] ?? 0,
                'tax_amount' => 0,
                'total_amount' => $lineTotal,
            ]);
        }

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'status' => 'draft',
            'previous_status' => null,
            'performed_by' => $request->user()->id,
        ]);

        return redirect()->route('orders.index')->with('success', 'Order created successfully');
    }

    public function show(Request $request, Order $order): Response
    {
        $order->load([
            'customer',
            'items.product',
            'items.unit',
            'statusHistory' => fn ($q) => $q->with('performedBy')->latest(),
            'assignedTo',
        ]);

        return Inertia::render('Orders/Show', [
            'order' => $order,
        ]);
    }

    public function edit(Request $request, Order $order): Response
    {
        $customers = Customer::where('company_id', $request->user()->company_id)->get();
        $products = Product::where('company_id', $request->user()->company_id)
            ->where('is_sellable', true)
            ->get();

        $order->load('items.product');

        return Inertia::render('Orders/Edit', [
            'order' => $order,
            'customers' => $customers,
            'products' => $products,
        ]);
    }

    public function update(Request $request, Order $order): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'order_type' => 'nullable|string|max:50',
            'priority' => 'nullable|string|max:50',
            'payment_terms_days' => 'nullable|integer|min:0',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'internal_notes' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        $subtotal = 0;
        $taxAmount = 0;
        $discountAmount = 0;

        foreach ($validated['items'] as $item) {
            $lineTotal = $item['quantity'] * $item['unit_price'];
            $itemDiscount = $lineTotal * (($item['discount_percentage'] ?? 0) / 100);
            $lineTotal -= $itemDiscount;
            $subtotal += $lineTotal;
            $discountAmount += $itemDiscount;
        }

        $order->update([
            'customer_id' => $validated['customer_id'],
            'order_type' => $validated['order_type'] ?? $order->order_type,
            'priority' => $validated['priority'] ?? $order->priority,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'total_amount' => $subtotal - $discountAmount + $taxAmount,
            'payment_terms_days' => $validated['payment_terms_days'] ?? $order->payment_terms_days,
            'due_date' => $validated['due_date'] ?? $order->due_date,
            'notes' => $validated['notes'] ?? $order->notes,
            'internal_notes' => $validated['internal_notes'] ?? $order->internal_notes,
            'assigned_to' => $validated['assigned_to'] ?? $order->assigned_to,
        ]);

        $order->items()->delete();

        foreach ($validated['items'] as $item) {
            $lineTotal = $item['quantity'] * $item['unit_price'];
            $itemDiscount = $lineTotal * (($item['discount_percentage'] ?? 0) / 100);
            $lineTotal -= $itemDiscount;

            $product = Product::find($item['product_id']);

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'sku' => $product->sku,
                'name' => $product->name,
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'discount_percentage' => $item['discount_percentage'] ?? 0,
                'tax_amount' => 0,
                'total_amount' => $lineTotal,
            ]);
        }

        return redirect()->route('orders.index')->with('success', 'Order updated successfully');
    }

    public function destroy(Request $request, Order $order): \Illuminate\Http\RedirectResponse
    {
        $order->items()->delete();
        $order->statusHistory()->delete();
        $order->delete();

        return redirect()->route('orders.index')->with('success', 'Order deleted successfully');
    }

    public function confirm(Request $request, Order $order): \Illuminate\Http\RedirectResponse
    {
        $previousStatus = $order->status->value;
        $order->update(['status' => 'confirmed']);

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'status' => 'confirmed',
            'previous_status' => $previousStatus,
            'notes' => $request->get('notes'),
            'performed_by' => $request->user()->id,
        ]);

        return redirect()->route('orders.show', $order)->with('success', 'Order confirmed successfully');
    }

    public function cancel(Request $request, Order $order): \Illuminate\Http\RedirectResponse
    {
        $previousStatus = $order->status->value;
        $order->update(['status' => 'cancelled']);

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'status' => 'cancelled',
            'previous_status' => $previousStatus,
            'notes' => $request->get('notes'),
            'performed_by' => $request->user()->id,
        ]);

        return redirect()->route('orders.show', $order)->with('success', 'Order cancelled successfully');
    }
}
