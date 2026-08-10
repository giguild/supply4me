<?php

namespace App\Models\Delivery;

use App\Enums\Delivery\DriverStatus;
use App\Models\Traits\HasCompany;
use App\Models\Traits\HasUuid;
use App\Models\Traits\SoftDeletes;
use Database\Factories\DriverFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Driver extends Model
{
    use HasCompany, HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'company_id',
        'user_id',
        'license_number',
        'license_expiry',
        'vehicle_type',
        'vehicle_registration',
        'phone',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'license_expiry' => 'date',
            'status' => DriverStatus::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Core\User::class);
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class);
    }
}
