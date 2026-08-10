<?php

namespace App\Http\Controllers\Inertia;

use App\Http\Controllers\Controller;
use App\Models\Delivery\Delivery;
use App\Models\Delivery\DeliveryRoute;
use App\Models\Delivery\DeliveryRouteStop;
use App\Models\Delivery\Driver;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DeliveryRouteController extends Controller
{
    public function index(Request $request): Response
    {
        $query = DeliveryRoute::where('company_id', $request->user()->company_id)
            ->with('driver.user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('route_number', 'like', "%{$search}%");
        }

        $routes = $query->latest()->paginate($request->get('per_page', 15));

        return Inertia::render('Delivery/Routes', [
            'routes' => $routes,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(Request $request): Response
    {
        $companyId = $request->user()->company_id;

        $drivers = Driver::where('company_id', $companyId)->with('user')->get();
        $deliveries = Delivery::where('company_id', $companyId)
            ->where('status', '!=', 'delivered')
            ->with('customer')
            ->get();

        return Inertia::render('Delivery/CreateRoute', [
            'drivers' => $drivers,
            'deliveries' => $deliveries,
        ]);
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'driver_id' => 'required|exists:drivers,id',
            'date' => 'required|date',
            'notes' => 'nullable|string',
            'stops' => 'required|array|min:1',
            'stops.*.delivery_id' => 'required|exists:deliveries,id',
            'stops.*.sequence' => 'required|integer|min:1',
        ]);

        $companyId = $request->user()->company_id;

        $route = DeliveryRoute::create([
            'company_id' => $companyId,
            'driver_id' => $validated['driver_id'],
            'date' => $validated['date'],
            'status' => 'planned',
            'notes' => $validated['notes'] ?? null,
        ]);

        foreach ($validated['stops'] as $stop) {
            DeliveryRouteStop::create([
                'route_id' => $route->id,
                'delivery_id' => $stop['delivery_id'],
                'sequence' => $stop['sequence'],
                'status' => 'pending',
            ]);
        }

        return redirect()->route('delivery-routes.index')->with('success', 'Delivery route created successfully');
    }

    public function show(Request $request, DeliveryRoute $deliveryRoute): Response
    {
        $deliveryRoute->load([
            'driver.user',
            'stops' => fn ($q) => $q->with('delivery.customer')->orderBy('sequence'),
        ]);

        return Inertia::render('Delivery/ShowRoute', [
            'route' => $deliveryRoute,
        ]);
    }

    public function edit(Request $request, DeliveryRoute $deliveryRoute): Response
    {
        $companyId = $request->user()->company_id;

        $drivers = Driver::where('company_id', $companyId)->with('user')->get();
        $deliveries = Delivery::where('company_id', $companyId)
            ->where('status', '!=', 'delivered')
            ->with('customer')
            ->get();

        $deliveryRoute->load('stops.delivery');

        return Inertia::render('Delivery/EditRoute', [
            'route' => $deliveryRoute,
            'drivers' => $drivers,
            'deliveries' => $deliveries,
        ]);
    }

    public function update(Request $request, DeliveryRoute $deliveryRoute): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'driver_id' => 'required|exists:drivers,id',
            'date' => 'required|date',
            'status' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'total_distance' => 'nullable|numeric|min:0',
            'total_time' => 'nullable|numeric|min:0',
        ]);

        $deliveryRoute->update($validated);

        if ($request->filled('stops')) {
            $deliveryRoute->stops()->delete();

            foreach ($request->stops as $stop) {
                DeliveryRouteStop::create([
                    'route_id' => $deliveryRoute->id,
                    'delivery_id' => $stop['delivery_id'],
                    'sequence' => $stop['sequence'],
                    'status' => $stop['status'] ?? 'pending',
                ]);
            }
        }

        return redirect()->route('delivery-routes.index')->with('success', 'Delivery route updated successfully');
    }

    public function destroy(Request $request, DeliveryRoute $deliveryRoute): \Illuminate\Http\RedirectResponse
    {
        $deliveryRoute->stops()->delete();
        $deliveryRoute->delete();

        return redirect()->route('delivery-routes.index')->with('success', 'Delivery route deleted successfully');
    }
}
