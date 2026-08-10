<?php

namespace App\Observers;

use App\Events\Companies\CompanyCreated;
use App\Events\Companies\CompanyUpdated;
use App\Models\Companies\Company;
use Spatie\Activitylog\Facades\ActivityLog;

class CompanyObserver
{
    public function created(Company $company): void
    {
        ActivityLog::event('Company created')
            ->on($company)
            ->withProperties([
                'company_id' => $company->id,
                'name' => $company->name,
                'slug' => $company->slug,
            ])
            ->log();

        CompanyCreated::dispatch($company);
    }

    public function updated(Company $company): void
    {
        $changes = $company->getChanges();

        ActivityLog::event('Company updated')
            ->on($company)
            ->withProperties([
                'company_id' => $company->id,
                'attributes' => $changes,
                'old' => $company->getOriginal(),
            ])
            ->log();

        CompanyUpdated::dispatch($company);
    }

    public function deleted(Company $company): void
    {
        ActivityLog::event('Company deleted')
            ->on($company)
            ->withProperties([
                'company_id' => $company->id,
                'name' => $company->name,
                'slug' => $company->slug,
            ])
            ->log();
    }

    public function restored(Company $company): void
    {
        ActivityLog::event('Company restored')
            ->on($company)
            ->withProperties([
                'company_id' => $company->id,
            ])
            ->log();
    }
}
