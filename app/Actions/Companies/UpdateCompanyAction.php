<?php

namespace App\Actions\Companies;

use App\Events\Companies\CompanyUpdated;
use App\Models\Companies\Company;

class UpdateCompanyAction
{
    public function execute(Company $company, array $data): Company
    {
        if (isset($data['name'])) {
            $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
        }

        $company->update($data);

        event(new CompanyUpdated($company));

        return $company->fresh();
    }
}
