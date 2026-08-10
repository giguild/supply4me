<?php

namespace App\Http\Controllers\Api\V1\Delivery;

use App\Actions\Delivery\AssignDriverAction;
use App\Actions\Delivery\StartDeliveryAction;
use App\Actions\Delivery\CompleteDeliveryAction;
use App\Actions\Delivery\RecordFailedAttemptAction;
use App\Http\Controllers\Controller;
use App\Models\Delivery\Delivery;
use App\Models\Delivery\DeliveryItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    public function __construct(
        protected AssignDriverAction $assignDriverAction,
        protected StartDeliveryAction $startDeliveryAction,
        protected CompleteDeliveryAction $completeDeliveryAction,
        protected RecordFailedAttemptAction $recordFailedAttemptAction
    ) {}

    public function index(Request $request): JsonResponse
    {
        $deliveries = Delivery::query()
            ->with(['driver', 'order', 'items.product', 'route'])
            ->when($request->search, fn ($q, $s) => $q->where('delivery_number', 'like', "%{$s}%"))
            ->when($request->driver_id, fn ($q, $d) => $q->where('driver_id', $d))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->date_from, fn ($q, $d) => $q->where('scheduled_date', '>=', $d))
            ->when($request->date_to, fn ($q, $d) => $q->where('scheduled_date', '<=', $d))
            ->latest()
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => 'Deliveries retrieved successfully',
            'data' => $deliveries,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'scheduled_date' => 'required|date|after_or_equal:today',
            'scheduled_time_start' => 'nullable|string|max:10',
            'scheduled_time_end' => 'nullable|string|max:10',
            'delivery_address' => 'required|array',
            'delivery_address.line1' => 'required|string|max:255',
            'delivery_address.line2' => 'nullable|string|max:255',
            'delivery_address.city' => 'required|string|max:100',
            'delivery_address.state' => 'required|string|max:100',
            'delivery_address.country' => 'required|string|max:100',
            'delivery_address.postal_code' => 'required|string|max:20',
            'delivery_address.latitude' => 'nullable|numeric|between:-90,90',
            'delivery_address.longitude' => 'nullable|numeric|between:-180,180',
            'contact_name' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
            'priority' => 'sometimes|string|in:low,normal,high,urgent',
        ]);

        $delivery = Delivery::create([
            'delivery_number' => 'DEL-' . strtoupper(uniqid()),
            'order_id' => $validated['order_id'],
            'status' => 'pending',
            'scheduled_date' => $validated['scheduled_date'],
            'scheduled_time_start' => $validated['scheduled_time_start'] ?? null,
            'scheduled_time_end' => $validated['scheduled_time_end'] ?? null,
            'delivery_address' => $validated['delivery_address'],
            'contact_name' => $validated['contact_name'] ?? null,
            'contact_phone' => $validated['contact_phone'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'priority' => $validated['priority'] ?? 'normal',
            'created_by' => auth()->id(),
        ]);

        return $this->created(
            $delivery->load(['order', 'items.product']),
            'Delivery created successfully'
        );
    }

    public function show(Delivery $delivery): JsonResponse
    {
        return $this->success(
            $delivery->load(['driver', 'order', 'items.product', 'route'])
        );
    }

    public function assignDriver(Request $request, Delivery $delivery): JsonResponse
    {
        $validated = $request->validate([
            'driver_id' => 'required|exists:drivers,id',
        ]);

        $delivery = $this->assignDriverAction->execute($delivery, $validated['driver_id']);

        return $this->success(
            $delivery->fresh()->load('driver'),
            'Driver assigned successfully'
        );
    }

    public function start(Delivery $delivery): JsonResponse
    {
        $delivery = $this->startDeliveryAction->execute($delivery);

        return $this->success(
            $delivery->fresh(),
            'Delivery started'
        );
    }

    public function complete(Request $request, Delivery $delivery): JsonResponse
    {
        $validated = $request->validate([
            'proof_of_delivery' => 'nullable|string|max:500',
            'recipient_name' => 'nullable|string|max:255',
            'signature' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:500',
            'items' => 'nullable|array',
            'items.*.delivery_item_id' => 'required|exists:delivery_items,id',
            'items.*.quantity_delivered' => 'required|integer|min:0',
            'items.*.condition' => 'sometimes|string|in:good,damaged',
        ]);

        $delivery = $this->completeDeliveryAction->execute($delivery, $validated);

        return $this->success(
            $delivery->fresh(),
            'Delivery completed successfully'
        );
    }

    public function fail(Request $request, Delivery $delivery): JsonResponse
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
            'condition' => 'sometimes|string|in:customer_unavailable,damaged,refused,incorrect_address,other',
            'notes' => 'nullable|string|max:500',
        ]);

        $delivery = $this->recordFailedAttemptAction->execute($delivery, $validated);

        return $this->success(
            $delivery->fresh(),
            'Failed delivery recorded'
        );
    }

    public function items(Delivery $delivery): JsonResponse
    {
        return $this->success($delivery->items()->with('product')->get());
    }
}
