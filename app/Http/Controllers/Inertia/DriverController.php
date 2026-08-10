<?php

namespace App\Http\Controllers\Inertia;

use App\Http\Controllers\Controller;
use App\Models\Core\User;
use App\Models\Delivery\Driver;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DriverController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Driver::where('company_id', $request->user()->company_id)
            ->with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('license_number', 'like', "%{$search}%")
                    ->orWhere('vehicle_registration', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $drivers = $query->latest()->paginate($request->get('per_page', 15));

        return Inertia::render('Delivery/Drivers', [
            'drivers' => $drivers,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create(Request $request): Response
    {
        $users = User::where('company_id', $request->user()->company_id)->get();

        return Inertia::render('Delivery/CreateDriver', [
            'users' => $users,
        ]);
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'license_number' => 'required|string|max:100',
            'license_expiry' => 'required|date',
            'vehicle_type' => 'nullable|string|max:100',
            'vehicle_registration' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:50',
            'status' => 'nullable|string',
        ]);

        $validated['company_id'] = $request->user()->company_id;

        Driver::create($validated);

        return redirect()->route('drivers.index')->with('success', 'Driver created successfully');
    }

    public function show(Request $request, Driver $driver): Response
    {
        $driver->load([
            'user',
            'deliveries' => fn ($q) => $q->with('customer')->latest()->limit(10),
        ]);

        return Inertia::render('Delivery/ShowDriver', [
            'driver' => $driver,
        ]);
    }

    public function edit(Request $request, Driver $driver): Response
    {
        $users = User::where('company_id', $request->user()->company_id)->get();

        return Inertia::render('Delivery/EditDriver', [
            'driver' => $driver,
            'users' => $users,
        ]);
    }

    public function update(Request $request, Driver $driver): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'license_number' => 'required|string|max:100',
            'license_expiry' => 'required|date',
            'vehicle_type' => 'nullable|string|max:100',
            'vehicle_registration' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:50',
            'status' => 'nullable|string',
        ]);

        $driver->update($validated);

        return redirect()->route('drivers.index')->with('success', 'Driver updated successfully');
    }

    public function destroy(Request $request, Driver $driver): \Illuminate\Http\RedirectResponse
    {
        $driver->delete();

        return redirect()->route('drivers.index')->with('success', 'Driver deleted successfully');
    }
}
