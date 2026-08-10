<?php

namespace App\Http\Controllers\Inertia;

use App\Http\Controllers\Controller;
use App\Models\Products\ProductUnit;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductUnitController extends Controller
{
    public function index(Request $request): Response
    {
        $units = ProductUnit::where('company_id', $request->user()->company_id)
            ->with('baseUnit')
            ->withCount('products')
            ->latest()
            ->paginate($request->get('per_page', 15));

        $baseUnits = ProductUnit::where('company_id', $request->user()->company_id)
            ->whereNull('base_unit_id')
            ->get();

        return Inertia::render('Products/Units', [
            'units' => $units,
            'baseUnits' => $baseUnits,
        ]);
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'required|string|max:20',
            'base_unit_id' => 'nullable|exists:product_units,id',
            'conversion_factor' => 'nullable|numeric|min:0',
            'status' => 'nullable|string',
        ]);

        $validated['company_id'] = $request->user()->company_id;

        ProductUnit::create($validated);

        return redirect()->route('product-units.index')->with('success', 'Unit created successfully');
    }

    public function update(Request $request, ProductUnit $productUnit): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'required|string|max:20',
            'base_unit_id' => 'nullable|exists:product_units,id',
            'conversion_factor' => 'nullable|numeric|min:0',
            'status' => 'nullable|string',
        ]);

        $productUnit->update($validated);

        return redirect()->route('product-units.index')->with('success', 'Unit updated successfully');
    }

    public function destroy(Request $request, ProductUnit $productUnit): \Illuminate\Http\RedirectResponse
    {
        $productUnit->delete();

        return redirect()->route('product-units.index')->with('success', 'Unit deleted successfully');
    }
}
