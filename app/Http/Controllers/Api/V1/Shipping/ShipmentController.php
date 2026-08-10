<?php

namespace App\Http\Controllers\Api\V1\Shipping;

use App\Http\Controllers\Controller;
use App\Models\Shipping\Shipment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShipmentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $shipments = Shipment::query()
            ->with(['order', 'carrier', 'items.product'])
            ->when($request->search, fn ($q, $s) => $q->where('shipment_number', 'like', "%{$s}%"))
            ->when($request->order_id, fn ($q, $o) => $q->where('order_id', $o))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->carrier_id, fn ($q, $c) => $q->where('carrier_id', $c))
            ->latest()
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => 'Shipments retrieved successfully',
            'data' => $shipments,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'carrier_id' => 'required|exists:shipping_carriers,id',
            'tracking_number' => 'nullable|string|max:100',
            'estimated_delivery_date' => 'nullable|date|after_or_equal:today',
            'shipping_address' => 'required|array',
            'shipping_address.line1' => 'required|string|max:255',
            'shipping_address.line2' => 'nullable|string|max:255',
            'shipping_address.city' => 'required|string|max:100',
            'shipping_address.state' => 'required|string|max:100',
            'shipping_address.country' => 'required|string|max:100',
            'shipping_address.postal_code' => 'required|string|max:20',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.order_item_id' => 'required|exists:order_items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $shipment = Shipment::create([
            'shipment_number' => 'SHP-' . strtoupper(uniqid()),
            'order_id' => $validated['order_id'],
            'carrier_id' => $validated['carrier_id'],
            'tracking_number' => $validated['tracking_number'] ?? null,
            'status' => 'pending',
            'estimated_delivery_date' => $validated['estimated_delivery_date'] ?? null,
            'shipping_address' => $validated['shipping_address'],
            'notes' => $validated['notes'] ?? null,
            'created_by' => auth()->id(),
        ]);

        foreach ($validated['items'] as $item) {
            $shipment->items()->create($item);
        }

        return $this->created(
            $shipment->load(['order', 'carrier', 'items.product']),
            'Shipment created successfully'
        );
    }

    public function show(Shipment $shipment): JsonResponse
    {
        return $this->success(
            $shipment->load(['order', 'carrier', 'items.product', 'createdBy'])
        );
    }

    public function ship(Shipment $shipment): JsonResponse
    {
        if ($shipment->status !== 'pending' && $shipment->status !== 'ready') {
            return $this->error('Shipment cannot be shipped in current status', 422);
        }

        $shipment->update([
            'status' => 'picked_up',
            'shipped_at' => now(),
        ]);

        return $this->success(
            $shipment->fresh(),
            'Shipment marked as shipped'
        );
    }

    public function deliver(Request $request, Shipment $shipment): JsonResponse
    {
        if (!in_array($shipment->status, ['in_transit', 'out_for_delivery'])) {
            return $this->error('Shipment cannot be delivered in current status', 422);
        }

        $validated = $request->validate([
            'proof_of_delivery' => 'nullable|string|max:500',
            'recipient_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        $shipment->update([
            'status' => 'delivered',
            'delivered_at' => now(),
            'proof_of_delivery' => $validated['proof_of_delivery'] ?? null,
            'recipient_name' => $validated['recipient_name'] ?? null,
        ]);

        return $this->success(
            $shipment->fresh(),
            'Shipment delivered successfully'
        );
    }

    public function track(Shipment $shipment): JsonResponse
    {
        return $this->success([
            'shipment_number' => $shipment->shipment_number,
            'tracking_number' => $shipment->tracking_number,
            'carrier' => $shipment->carrier->name,
            'status' => $shipment->status,
            'status_label' => $shipment->status->label(),
            'estimated_delivery_date' => $shipment->estimated_delivery_date,
            'shipped_at' => $shipment->shipped_at,
            'delivered_at' => $shipment->delivered_at,
            'tracking_url' => $shipment->tracking_url,
        ]);
    }
}
