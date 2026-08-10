<?php

namespace App\Models\Delivery;

use App\Models\Traits\HasUuid;
use Database\Factories\DeliveryRouteStopFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryRouteStop extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'route_id',
        'delivery_id',
        'sequence',
        'status',
        'arrival_time',
        'departure_time',
        'signature',
        'proof_of_delivery',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'arrival_time' => 'datetime',
            'departure_time' => 'datetime',
        ];
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(DeliveryRoute::class, 'route_id');
    }

    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class);
    }
}
