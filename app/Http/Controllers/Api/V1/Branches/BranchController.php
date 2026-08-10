<?php

namespace App\Http\Controllers\Api\V1\Branches;

use App\Http\Controllers\Controller;
use App\Models\Companies\Company;
use App\Models\Branches\Branch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index(Request $request, Company $company): JsonResponse
    {
        $branches = Branch::where('company_id', $company->id)
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => 'Branches retrieved successfully',
            'data' => $branches,
        ]);
    }

    public function store(Request $request, Company $company): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:branches,code,' . $company->id . ',company_id',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'type' => 'sometimes|string|in:main,warehouse,office,retail',
            'is_default' => 'sometimes|boolean',
        ]);

        $validated['company_id'] = $company->id;
        $branch = Branch::create($validated);

        return $this->created($branch, 'Branch created successfully');
    }

    public function show(Company $company, Branch $branch): JsonResponse
    {
        $this->authorizeBranch($company, $branch);

        return $this->success($branch->load(['warehouses']));
    }

    public function update(Request $request, Company $company, Branch $branch): JsonResponse
    {
        $this->authorizeBranch($company, $branch);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'code' => 'sometimes|string|max:50|unique:branches,code,' . $branch->id,
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'type' => 'sometimes|string|in:main,warehouse,office,retail',
            'is_default' => 'sometimes|boolean',
            'status' => 'sometimes|string|in:active,inactive',
        ]);

        $branch->update($validated);

        return $this->success($branch->fresh(), 'Branch updated successfully');
    }

    public function destroy(Company $company, Branch $branch): JsonResponse
    {
        $this->authorizeBranch($company, $branch);

        $branch->delete();

        return $this->noContent('Branch deleted successfully');
    }

    protected function authorizeBranch(Company $company, Branch $branch): void
    {
        if ($branch->company_id !== $company->id) {
            abort(403, 'Branch does not belong to this company');
        }
    }
}
