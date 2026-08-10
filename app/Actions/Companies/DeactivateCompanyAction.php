<?php

namespace App\Actions\Companies;

use App\Enums\Companies\CompanyStatus;
use App\Models\Companies\Company;

class DeactivateCompanyAction
{
    public function execute(Company $company): bool
    {
        $company->update([
            'status' => CompanyStatus::Inactive,
        ]);

        $company->delete();

        return true;
    }
}
