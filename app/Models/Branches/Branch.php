<?php

namespace App\Models\Branches;

use App\Enums\Companies\BranchStatus;
use App\Models\Traits\HasCompany;
use App\Models\Traits\HasUuid;
use App\Models\Traits\SoftDeletes;
use Database\Factories\BranchFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    use HasCompany, HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'company_id',
        'name',
        'code',
        'type',
        'email',
        'phone',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'postal_code',
        'country',
        'latitude',
        'longitude',
        'is_main',
        'status',
        'operating_hours',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'is_main' => 'boolean',
            'operating_hours' => 'array',
            'status' => BranchStatus::class,
        ];
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Core\User::class, 'user_branches')
            ->withPivot('is_default');
    }

    public function warehouses(): HasMany
    {
        return $this->hasMany(\App\Models\Inventory\Warehouse::class);
    }
}
