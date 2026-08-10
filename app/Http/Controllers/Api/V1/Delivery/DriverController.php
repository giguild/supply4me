<?php

namespace App\Http\Controllers\Api\V1\Delivery;

use App\Http\Controllers\Controller;
use App\Models\Delivery\Driver;
use App\Resources\Delivery\DriverResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $drivers = Driver::query()
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%")->orWhere('phone', 'like', "%{$s}%"))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->paginate($request->get('per_page', 15));

        return $this->paginated($drivers, DriverResource::collection($drivers->items()));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'license_number' => 'required|string|max:50',
            'vehicle_type' => 'nullable|string|max:100',
            'vehicle_registration' => 'nullable|string|max:50',
            'status' => 'sometimes|string|in:active,inactive,on_leave',
            'notes' => 'nullable|string|max:500',
        ]);

        $driver = Driver::create($validated);

        return $this->created(
            new DriverResource($driver),
            'Driver created successfully'
        );
    }

    public function show(Driver $driver): JsonResponse
    {
        return $this->success(
            new DriverResource($driver->load(['deliveries' => fn ($q) => $q->latest()->limit(10)]))
        );
    }

    public function update(Request $request, Driver $driver): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:50',
            'email' => 'nullable|email|max:255',
            'license_number' => 'sometimes|string|max:50',
            'vehicle_type' => 'nullable|string|max:100',
            'vehicle_registration' => 'nullable|string|max:50',
            'status' => 'sometimes|string|in:active,inactive,on_leave',
            'notes' => 'nullable|string|max:500',
        ]);

        $driver->update($validated);

        return $this->success(
            new DriverResource($driver->fresh()),
            'Driver updated successfully'
        );
    }

    public function destroy(Driver $driver): JsonResponse
    {
        $hasActiveDeliveries = $driver->deliveries()
            ->whereIn('status', ['assigned', 'out_for_delivery'])
            ->exists();

        if ($hasActiveDeliveries) {
            return $this->error('Cannot delete driver with active deliveries', 422);
        }

        $driver->delete();

        return $this->noContent('Driver deleted successfully');
    }

    public function location(Driver $driver): JsonResponse
    {
        $lastLocation = $driver->locations()->latest()->first();

        return $this->success([
            'driver_id' => $driver->id,
            'latitude' => $lastLocation?->latitude,
            'longitude' => $lastLocation?->longitude,
            'timestamp' => $lastLocation?->created_at,
            'speed' => $lastLocation?->speed,
        ]);
    }

    public function updateLocation(Request $request, Driver $driver): JsonResponse
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'speed' => 'nullable|numeric|min:0',
        ]);

        $driver->locations()->create($validated);

        return $this->success(message: 'Location updated successfully');
    }
}
