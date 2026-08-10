<?php

namespace App\Models\Traits;

use App\Models\Companies\Company;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

trait HasCompany
{
    public static function bootHasCompany(): void
    {
        static::creating(function (Model $model) {
            if (is_null($model->company_id) && Auth::check()) {
                $model->company_id = Auth::user()->company_id;
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeForCompany(Builder $query, ?int $companyId = null): Builder
    {
        $companyId = $companyId ?? Auth::user()->company_id ?? null;

        return $query->where('company_id', $companyId);
    }
}
