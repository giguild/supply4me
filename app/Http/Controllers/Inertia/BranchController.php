<?php

namespace App\Http\Controllers\Inertia;

use App\Http\Controllers\Controller;
use App\Models\Branches\Branch;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BranchController extends Controller
{
    public function index(Request $request): Response
    {
        $branches = Branch::where('company_id', $request->user()->company_id)
            ->withCount('users', 'warehouses')
            ->latest()
            ->paginate($request->get('per_page', 15));

        return Inertia::render('Branches/Index', [
            'branches' => $branches,
        ]);
    }

    public function create(Request $request): Response
    {
        return Inertia::render('Branches/Create');
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50',
            'type' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'is_main' => 'nullable|boolean',
            'status' => 'nullable|string',
            'operating_hours' => 'nullable|array',
        ]);

        $validated['company_id'] = $request->user()->company_id;

        Branch::create($validated);

        return redirect()->route('branches.index')->with('success', 'Branch created successfully');
    }

    public function show(Request $request, Branch $branch): Response
    {
        $branch->load(['users', 'warehouses']);

        return Inertia::render('Branches/Show', [
            'branch' => $branch,
        ]);
    }

    public function edit(Request $request, Branch $branch): Response
    {
        return Inertia::render('Branches/Edit', [
            'branch' => $branch,
        ]);
    }

    public function update(Request $request, Branch $branch): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50',
            'type' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'is_main' => 'nullable|boolean',
            'status' => 'nullable|string',
            'operating_hours' => 'nullable|array',
        ]);

        $branch->update($validated);

        return redirect()->route('branches.index')->with('success', 'Branch updated successfully');
    }

    public function destroy(Request $request, Branch $branch): \Illuminate\Http\RedirectResponse
    {
        $branch->delete();

        return redirect()->route('branches.index')->with('success', 'Branch deleted successfully');
    }
}
