<?php

namespace App\Http\Controllers\Inertia;

use App\Http\Controllers\Controller;
use App\Models\Branches\Branch;
use App\Models\Inventory\Warehouse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WarehouseController extends Controller
{
    public function index(Request $request): Response
    {
        $companyId = $request->user()->company_id;

        $query = Warehouse::where('company_id', $companyId)->with('branch');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $warehouses = $query->latest()->paginate($request->get('per_page', 15));

        return Inertia::render('Warehouses/Index', [
            'warehouses' => $warehouses,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(Request $request): Response
    {
        $branches = Branch::where('company_id', $request->user()->company_id)->get();

        return Inertia::render('Warehouses/Create', [
            'branches' => $branches,
        ]);
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:warehouses,code,NULL,id,company_id,' . $request->user()->company_id,
            'branch_id' => 'nullable|exists:branches,id',
            'type' => 'required|in:main,regional,temporary,dropship',
            'address_line_1' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'capacity' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $validated['company_id'] = $request->user()->company_id;

        Warehouse::create($validated);

        return redirect()->route('warehouses.index')->with('success', 'Warehouse created successfully');
    }

    public function show(Request $request, Warehouse $warehouse): Response
    {
        $warehouse->load(['branch', 'stockItems.product']);

        return Inertia::render('Warehouses/Show', [
            'warehouse' => $warehouse,
        ]);
    }

    public function edit(Request $request, Warehouse $warehouse): Response
    {
        $branches = Branch::where('company_id', $request->user()->company_id)->get();

        return Inertia::render('Warehouses/Edit', [
            'warehouse' => $warehouse,
            'branches' => $branches,
        ]);
    }

    public function update(Request $request, Warehouse $warehouse): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:warehouses,code,' . $warehouse->id . ',id,company_id,' . $request->user()->company_id,
            'branch_id' => 'nullable|exists:branches,id',
            'type' => 'required|in:main,regional,temporary,dropship',
            'address_line_1' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'capacity' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $warehouse->update($validated);

        return redirect()->route('warehouses.index')->with('success', 'Warehouse updated successfully');
    }

    public function destroy(Request $request, Warehouse $warehouse): \Illuminate\Http\RedirectResponse
    {
        $warehouse->delete();

        return redirect()->route('warehouses.index')->with('success', 'Warehouse deleted successfully');
    }
}
