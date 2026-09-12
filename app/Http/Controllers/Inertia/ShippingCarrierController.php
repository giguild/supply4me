<?php

namespace App\Http\Controllers\Inertia;

use App\Http\Controllers\Controller;
use App\Models\Shipping\ShippingCarrier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ShippingCarrierController extends Controller
{
    public function index(Request $request): Response
    {
        $query = ShippingCarrier::where('company_id', $request->user()->company_id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $carriers = $query->latest()->paginate($request->get('per_page', 15));

        return Inertia::render('ShippingCarriers/Index', [
            'carriers' => $carriers,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('ShippingCarriers/Create');
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('shipping_carriers', 'code')->where('company_id', $request->user()->company_id),
            ],
            'api_key' => 'nullable|string|max:255',
            'api_secret' => 'nullable|string|max:255',
            'tracking_url' => 'nullable|string|max:500',
            'status' => 'nullable|string|in:active,inactive',
        ]);

        $validated['company_id'] = $request->user()->company_id;
        $validated['status'] = $validated['status'] ?? 'active';

        ShippingCarrier::create($validated);

        return redirect()->route('shipping-carriers.index')->with('success', 'Carrier created successfully');
    }

    public function edit(Request $request, ShippingCarrier $shippingCarrier): Response
    {
        return Inertia::render('ShippingCarriers/Edit', [
            'carrier' => $shippingCarrier,
        ]);
    }

    public function update(Request $request, ShippingCarrier $shippingCarrier): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('shipping_carriers', 'code')
                    ->where('company_id', $request->user()->company_id)
                    ->ignore($shippingCarrier->id),
            ],
            'api_key' => 'nullable|string|max:255',
            'api_secret' => 'nullable|string|max:255',
            'tracking_url' => 'nullable|string|max:500',
            'status' => 'nullable|string|in:active,inactive',
        ]);

        $shippingCarrier->update($validated);

        return redirect()->route('shipping-carriers.index')->with('success', 'Carrier updated successfully');
    }

    public function destroy(Request $request, ShippingCarrier $shippingCarrier): \Illuminate\Http\RedirectResponse
    {
        if ($shippingCarrier->shipments()->exists()) {
            return redirect()->route('shipping-carriers.index')
                ->with('error', 'Cannot delete carrier with existing shipments');
        }

        $shippingCarrier->delete();

        return redirect()->route('shipping-carriers.index')->with('success', 'Carrier deleted successfully');
    }
}