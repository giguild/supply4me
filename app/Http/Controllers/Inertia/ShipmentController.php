<?php

namespace App\Http\Controllers\Inertia;

use App\Http\Controllers\Controller;
use App\Models\Inventory\Warehouse;
use App\Models\Orders\Order;
use App\Models\Shipping\Shipment;
use App\Models\Shipping\ShipmentItem;
use App\Models\Shipping\ShippingCarrier;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ShipmentController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Shipment::where('company_id', $request->user()->company_id)
            ->with(['order', 'warehouse', 'carrier']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('shipment_number', 'like', "%{$search}%")
                    ->orWhere('tracking_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $shipments = $query->latest()->paginate($request->get('per_page', 15));

        return Inertia::render('Shipping/Index', [
            'shipments' => $shipments,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create(Request $request): Response
    {
        $companyId = $request->user()->company_id;

        $warehouses = Warehouse::where('company_id', $companyId)->get();
        $carriers = ShippingCarrier::where('company_id', $companyId)->get();
        $orders = Order::where('company_id', $companyId)
            ->whereIn('status', ['confirmed', 'processing', 'picking', 'packing', 'ready_to_ship'])
            ->with('customer')
            ->get();

        return Inertia::render('Shipping/Create', [
            'warehouses' => $warehouses,
            'carriers' => $carriers,
            'orders' => $orders,
        ]);
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'carrier_id' => 'nullable|exists:shipping_carriers,id',
            'tracking_number' => 'nullable|string|max:255',
            'shipping_method' => 'nullable|string|max:100',
            'estimated_delivery_date' => 'nullable|date',
            'shipping_cost' => 'nullable|numeric|min:0',
            'weight' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.order_item_id' => 'required|exists:order_items,id',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.weight' => 'nullable|numeric|min:0',
        ]);

        $companyId = $request->user()->company_id;

        $shipment = Shipment::create([
            'company_id' => $companyId,
            'order_id' => $validated['order_id'],
            'warehouse_id' => $validated['warehouse_id'],
            'carrier_id' => $validated['carrier_id'] ?? null,
            'status' => 'pending',
            'tracking_number' => $validated['tracking_number'] ?? null,
            'shipping_method' => $validated['shipping_method'] ?? null,
            'estimated_delivery_date' => $validated['estimated_delivery_date'] ?? null,
            'shipping_cost' => $validated['shipping_cost'] ?? 0,
            'weight' => $validated['weight'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        foreach ($validated['items'] as $item) {
            ShipmentItem::create([
                'shipment_id' => $shipment->id,
                'order_item_id' => $item['order_item_id'],
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'weight' => $item['weight'] ?? null,
            ]);
        }

        return redirect()->route('shipments.index')->with('success', 'Shipment created successfully');
    }

    public function show(Request $request, Shipment $shipment): Response
    {
        $shipment->load([
            'order.customer',
            'warehouse',
            'carrier',
            'items.product',
            'items.orderItem',
        ]);

        return Inertia::render('Shipping/Show', [
            'shipment' => $shipment,
        ]);
    }

    public function edit(Request $request, Shipment $shipment): Response
    {
        $companyId = $request->user()->company_id;

        $warehouses = Warehouse::where('company_id', $companyId)->get();
        $carriers = ShippingCarrier::where('company_id', $companyId)->get();
        $orders = Order::where('company_id', $companyId)
            ->whereIn('status', ['confirmed', 'processing', 'picking', 'packing', 'ready_to_ship'])
            ->with('customer')
            ->get();

        $shipment->load('items.product');

        return Inertia::render('Shipping/Edit', [
            'shipment' => $shipment,
            'warehouses' => $warehouses,
            'carriers' => $carriers,
            'orders' => $orders,
        ]);
    }

    public function update(Request $request, Shipment $shipment): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'carrier_id' => 'nullable|exists:shipping_carriers,id',
            'tracking_number' => 'nullable|string|max:255',
            'shipping_method' => 'nullable|string|max:100',
            'estimated_delivery_date' => 'nullable|date',
            'shipping_cost' => 'nullable|numeric|min:0',
            'weight' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'status' => 'nullable|string|max:50',
        ]);

        $shipment->update($validated);

        return redirect()->route('shipments.index')->with('success', 'Shipment updated successfully');
    }

    public function track(Request $request, Shipment $shipment): Response
    {
        $shipment->load([
            'order.customer',
            'carrier',
            'items.product',
        ]);

        return Inertia::render('Shipping/Track', [
            'shipment' => $shipment,
        ]);
    }
}
