<?php

namespace App\Http\Controllers\Api\V1\Shipping;

use App\Http\Controllers\Controller;
use App\Models\Shipping\ShippingCarrier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShippingCarrierController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $carriers = ShippingCarrier::query()
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => 'Carriers retrieved successfully',
            'data' => $carriers,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:shipping_carriers,code',
            'api_key' => 'nullable|string|max:255',
            'api_secret' => 'nullable|string|max:255',
            'tracking_url' => 'nullable|url|max:500',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'status' => 'sometimes|string|in:active,inactive',
        ]);

        $carrier = ShippingCarrier::create($validated);

        return $this->created($carrier, 'Carrier created successfully');
    }

    public function show(ShippingCarrier $shippingCarrier): JsonResponse
    {
        return $this->success($shippingCarrier);
    }

    public function update(Request $request, ShippingCarrier $shippingCarrier): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'code' => 'sometimes|string|max:50|unique:shipping_carriers,code,' . $shippingCarrier->id,
            'api_key' => 'nullable|string|max:255',
            'api_secret' => 'nullable|string|max:255',
            'tracking_url' => 'nullable|url|max:500',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'status' => 'sometimes|string|in:active,inactive',
        ]);

        $shippingCarrier->update($validated);

        return $this->success(
            $shippingCarrier->fresh(),
            'Carrier updated successfully'
        );
    }

    public function destroy(ShippingCarrier $shippingCarrier): JsonResponse
    {
        if ($shippingCarrier->shipments()->exists()) {
            return $this->error('Cannot delete carrier with existing shipments', 422);
        }

        $shippingCarrier->delete();

        return $this->noContent('Carrier deleted successfully');
    }
}
