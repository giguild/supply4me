<?php

namespace App\Actions\Companies;

use App\Enums\Companies\BranchStatus;
use App\Enums\Companies\BranchType;
use App\Enums\Companies\CompanyStatus;
use App\Enums\Core\UserRole;
use App\Events\Companies\CompanyCreated;
use App\Models\Branches\Branch;
use App\Models\Companies\Company;
use App\Models\Core\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateCompanyAction
{
    public function execute(array $data, User $adminUser): Company
    {
        return DB::transaction(function () use ($data, $adminUser) {
            $company = Company::create([
                'name' => $data['name'],
                'slug' => Str::slug($data['name']),
                'registration_number' => $data['registration_number'] ?? null,
                'tax_number' => $data['tax_number'] ?? null,
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'address_line_1' => $data['address_line_1'] ?? null,
                'address_line_2' => $data['address_line_2'] ?? null,
                'city' => $data['city'] ?? null,
                'state' => $data['state'] ?? null,
                'postal_code' => $data['postal_code'] ?? null,
                'country' => $data['country'] ?? null,
                'currency_code' => $data['currency_code'] ?? 'USD',
                'status' => CompanyStatus::Active,
                'settings' => $data['settings'] ?? [],
            ]);

            $branch = Branch::create([
                'company_id' => $company->id,
                'name' => $data['name'] . ' - Main',
                'code' => 'MAIN',
                'type' => BranchType::Headquarters,
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'address_line_1' => $data['address_line_1'] ?? null,
                'address_line_2' => $data['address_line_2'] ?? null,
                'city' => $data['city'] ?? null,
                'state' => $data['state'] ?? null,
                'postal_code' => $data['postal_code'] ?? null,
                'country' => $data['country'] ?? null,
                'is_main' => true,
                'status' => BranchStatus::Active,
            ]);

            $adminUser->update([
                'company_id' => $company->id,
            ]);

            $adminUser->branches()->attach($branch->id, ['is_default' => true]);

            if (! isset($adminUser->role) || $adminUser->role !== UserRole::SuperAdmin) {
                $adminUser->update(['role' => UserRole::Admin]);
            }

            event(new CompanyCreated($company));

            return $company;
        });
    }
}
