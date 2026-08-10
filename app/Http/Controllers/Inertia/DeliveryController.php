<?php

namespace App\Http\Controllers\Inertia;

use App\Http\Controllers\Controller;
use App\Models\Customers\Customer;
use App\Models\Delivery\Delivery;
use App\Models\Delivery\DeliveryItem;
use App\Models\Delivery\Driver;
use App\Models\Orders\Order;
use App\Models\Shipping\Shipment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DeliveryController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Delivery::where('company_id', $request->user()->company_id)
            ->with(['customer', 'driver', 'order', 'shipment']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('delivery_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn ($cq) => $cq->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $deliveries = $query->latest()->paginate($request->get('per_page', 15));

        return Inertia::render('Delivery/Index', [
            'deliveries' => $deliveries,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create(Request $request): Response
    {
        $companyId = $request->user()->company_id;

        $customers = Customer::where('company_id', $companyId)->get();
        $drivers = Driver::where('company_id', $companyId)->get();
        $orders = Order::where('company_id', $companyId)
            ->whereIn('status', ['confirmed', 'processing', 'ready_to_ship', 'shipped'])
            ->with('customer')
            ->get();
        $shipments = Shipment::where('company_id', $companyId)
            ->whereNotIn('status', ['delivered', 'cancelled'])
            ->get();

        return Inertia::render('Delivery/Create', [
            'customers' => $customers,
            'drivers' => $drivers,
            'orders' => $orders,
            'shipments' => $shipments,
        ]);
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'order_id' => 'nullable|exists:orders,id',
            'shipment_id' => 'nullable|exists:shipments,id',
            'driver_id' => 'nullable|exists:drivers,id',
            'customer_id' => 'required|exists:customers,id',
            'scheduled_date' => 'required|date',
            'estimated_time' => 'nullable|string|max:50',
            'signature_required' => 'nullable|boolean',
            'delivery_notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.order_item_id' => 'nullable|exists:order_items,id',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ]);

        $companyId = $request->user()->company_id;

        $delivery = Delivery::create([
            'company_id' => $companyId,
            'order_id' => $validated['order_id'] ?? null,
            'shipment_id' => $validated['shipment_id'] ?? null,
            'driver_id' => $validated['driver_id'] ?? null,
            'customer_id' => $validated['customer_id'],
            'status' => 'pending',
            'scheduled_date' => $validated['scheduled_date'],
            'estimated_time' => $validated['estimated_time'] ?? null,
            'signature_required' => $validated['signature_required'] ?? false,
            'delivery_notes' => $validated['delivery_notes'] ?? null,
        ]);

        foreach ($validated['items'] as $item) {
            DeliveryItem::create([
                'delivery_id' => $delivery->id,
                'order_item_id' => $item['order_item_id'] ?? null,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'quantity_delivered' => 0,
            ]);
        }

        return redirect()->route('deliveries.index')->with('success', 'Delivery created successfully');
    }

    public function show(Request $request, Delivery $delivery): Response
    {
        $delivery->load([
            'customer',
            'driver.user',
            'order',
            'shipment',
            'items.product',
            'items.orderItem',
        ]);

        return Inertia::render('Delivery/Show', [
            'delivery' => $delivery,
        ]);
    }

    public function edit(Request $request, Delivery $delivery): Response
    {
        $companyId = $request->user()->company_id;

        $customers = Customer::where('company_id', $companyId)->get();
        $drivers = Driver::where('company_id', $companyId)->get();
        $orders = Order::where('company_id', $companyId)
            ->whereIn('status', ['confirmed', 'processing', 'ready_to_ship', 'shipped'])
            ->get();
        $shipments = Shipment::where('company_id', $companyId)->get();

        $delivery->load('items.product');

        return Inertia::render('Delivery/Edit', [
            'delivery' => $delivery,
            'customers' => $customers,
            'drivers' => $drivers,
            'orders' => $orders,
            'shipments' => $shipments,
        ]);
    }

    public function update(Request $request, Delivery $delivery): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'order_id' => 'nullable|exists:orders,id',
            'shipment_id' => 'nullable|exists:shipments,id',
            'driver_id' => 'nullable|exists:drivers,id',
            'customer_id' => 'required|exists:customers,id',
            'scheduled_date' => 'required|date',
            'estimated_time' => 'nullable|string|max:50',
            'actual_delivery_date' => 'nullable|date',
            'delivery_time' => 'nullable|date',
            'signature_required' => 'nullable|boolean',
            'delivery_notes' => 'nullable|string',
            'failure_reason' => 'nullable|string',
            'status' => 'nullable|string|max:50',
        ]);

        $delivery->update($validated);

        return redirect()->route('deliveries.index')->with('success', 'Delivery updated successfully');
    }
}
