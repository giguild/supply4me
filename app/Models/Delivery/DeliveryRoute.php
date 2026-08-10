<?php

namespace App\Models\Delivery;

use App\Enums\Delivery\RouteStatus;
use App\Models\Traits\HasCompany;
use App\Models\Traits\HasNumber;
use App\Models\Traits\HasUuid;
use Database\Factories\DeliveryRouteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryRoute extends Model
{
    use HasCompany, HasFactory, HasNumber, HasUuid;

    public const PREFIX = 'RTE';

    protected $fillable = [
        'company_id',
        'route_number',
        'driver_id',
        'date',
        'status',
        'started_at',
        'completed_at',
        'total_distance',
        'total_time',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'total_distance' => 'decimal:2',
            'total_time' => 'decimal:2',
            'status' => RouteStatus::class,
        ];
    }

    public function getNumberPrefix(): string
    {
        return self::PREFIX;
    }

    public function getNumberColumn(): string
    {
        return 'route_number';
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function stops(): HasMany
    {
        return $this->hasMany(DeliveryRouteStop::class, 'route_id');
    }
}
