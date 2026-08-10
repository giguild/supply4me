<?php

namespace App\Http\Controllers\Api\V1\Delivery;

use App\Actions\Delivery\CreateDeliveryRouteAction;
use App\Http\Controllers\Controller;
use App\Models\Delivery\DeliveryRoute;
use App\Models\Delivery\DeliveryRouteStop;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeliveryRouteController extends Controller
{
    public function __construct(
        protected CreateDeliveryRouteAction $createDeliveryRouteAction
    ) {}

    public function index(Request $request): JsonResponse
    {
        $routes = DeliveryRoute::query()
            ->with(['driver', 'deliveries', 'stops'])
            ->when($request->search, fn ($q, $s) => $q->where('route_number', 'like', "%{$s}%"))
            ->when($request->driver_id, fn ($q, $d) => $q->where('driver_id', $d))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->date, fn ($q, $d) => $q->whereDate('date', $d))
            ->latest()
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => 'Delivery routes retrieved successfully',
            'data' => $routes,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'driver_id' => 'required|exists:drivers,id',
            'date' => 'required|date|after_or_equal:today',
            'notes' => 'nullable|string|max:1000',
            'delivery_ids' => 'required|array|min:1',
            'delivery_ids.*' => 'exists:deliveries,id',
        ]);

        $route = $this->createDeliveryRouteAction->execute($validated);

        return $this->created(
            $route->load(['driver', 'deliveries', 'stops']),
            'Delivery route created successfully'
        );
    }

    public function show(DeliveryRoute $deliveryRoute): JsonResponse
    {
        return $this->success(
            $deliveryRoute->load(['driver', 'deliveries.items.product', 'stops.delivery'])
        );
    }

    public function start(DeliveryRoute $deliveryRoute): JsonResponse
    {
        if ($deliveryRoute->status !== 'pending') {
            return $this->error('Only pending routes can be started', 422);
        }

        $deliveryRoute->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        return $this->success(
            $deliveryRoute->fresh(),
            'Route started'
        );
    }

    public function complete(DeliveryRoute $deliveryRoute): JsonResponse
    {
        if ($deliveryRoute->status !== 'in_progress') {
            return $this->error('Only in-progress routes can be completed', 422);
        }

        $deliveryRoute->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return $this->success(
            $deliveryRoute->fresh(),
            'Route completed'
        );
    }

    public function stops(DeliveryRoute $deliveryRoute): JsonResponse
    {
        return $this->success(
            $deliveryRoute->stops()->with('delivery')->orderBy('sequence')->get()
        );
    }

    public function updateStop(Request $request, DeliveryRoute $deliveryRoute, DeliveryRouteStop $stop): JsonResponse
    {
        if ($stop->route_id !== $deliveryRoute->id) {
            abort(403, 'Stop does not belong to this route');
        }

        $validated = $request->validate([
            'status' => 'sometimes|string|in:pending,arrived,delivered,skipped',
            'notes' => 'nullable|string|max:255',
            'actual_arrival' => 'nullable|date',
            'proof_of_delivery' => 'nullable|string|max:500',
        ]);

        $stop->update($validated);

        if ($validated['status'] === 'arrived') {
            $stop->update(['actual_arrival' => now()]);
        }

        return $this->success(
            $stop->fresh()->load('delivery'),
            'Stop updated successfully'
        );
    }
}
