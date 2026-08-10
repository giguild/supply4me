<?php

namespace App\Http\Controllers\Api\V1\Companies;

use App\Actions\Companies\CreateCompanyAction;
use App\Actions\Companies\DeactivateCompanyAction;
use App\Actions\Companies\UpdateCompanyAction;
use App\Http\Controllers\Controller;
use App\Models\Companies\Company;
use App\Resources\Companies\CompanyResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function __construct(
        protected CreateCompanyAction $createCompanyAction,
        protected UpdateCompanyAction $updateCompanyAction,
        protected DeactivateCompanyAction $deactivateCompanyAction
    ) {}

    public function index(Request $request): JsonResponse
    {
        $companies = Company::query()
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->paginate($request->get('per_page', 15));

        return $this->paginated($companies, CompanyResource::collection($companies->items()));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:companies,email',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'tax_number' => 'nullable|string|max:50',
            'registration_number' => 'nullable|string|max:50',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|max:2048',
        ]);

        $company = $this->createCompanyAction->execute($validated);

        return $this->created(
            new CompanyResource($company),
            'Company created successfully'
        );
    }

    public function show(Company $company): JsonResponse
    {
        return $this->success(
            new CompanyResource($company->load(['branches', 'settings']))
        );
    }

    public function update(Request $request, Company $company): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:companies,email,' . $company->id,
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'tax_number' => 'nullable|string|max:50',
            'registration_number' => 'nullable|string|max:50',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|max:2048',
        ]);

        $company = $this->updateCompanyAction->execute($company, $validated);

        return $this->success(
            new CompanyResource($company),
            'Company updated successfully'
        );
    }

    public function destroy(Company $company): JsonResponse
    {
        $this->deactivateCompanyAction->execute($company);

        return $this->noContent('Company deactivated successfully');
    }

    public function settings(Company $company): JsonResponse
    {
        return $this->success(
            new CompanyResource($company->load('settings'))
        );
    }

    public function updateSettings(Request $request, Company $company): JsonResponse
    {
        $validated = $request->validate([
            'currency' => 'sometimes|string|max:3',
            'timezone' => 'sometimes|string|max:50',
            'date_format' => 'sometimes|string|max:20',
            'tax_rate' => 'sometimes|numeric|min:0|max:100',
            'low_stock_threshold' => 'sometimes|integer|min:0',
            'auto_generate_invoice' => 'sometimes|boolean',
            'payment_terms_days' => 'sometimes|integer|min:0',
        ]);

        $company->settings()->updateOrCreate(
            ['company_id' => $company->id],
            $validated
        );

        return $this->success(
            new CompanyResource($company->fresh('settings')),
            'Company settings updated successfully'
        );
    }
}
