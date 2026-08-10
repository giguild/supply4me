<?php

namespace App\Models\Traits;

use App\Models\Branches\Branch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

trait HasBranch
{
    public static function bootHasBranch(): void
    {
        static::creating(function (Model $model) {
            if (is_null($model->branch_id) && Auth::check()) {
                $model->branch_id = Auth::user()->branch_id ?? null;
            }
        });
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function scopeForBranch(Builder $query, ?int $branchId = null): Builder
    {
        $branchId = $branchId ?? Auth::user()->branch_id ?? null;

        return $query->where('branch_id', $branchId);
    }
}
