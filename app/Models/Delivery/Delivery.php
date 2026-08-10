<?php

namespace App\Models\Delivery;

use App\Enums\Delivery\DeliveryStatus;
use App\Models\Traits\HasCompany;
use App\Models\Traits\HasNumber;
use App\Models\Traits\HasUuid;
use App\Models\Traits\SoftDeletes;
use Database\Factories\DeliveryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Delivery extends Model
{
    use HasCompany, HasFactory, HasNumber, HasUuid, SoftDeletes;

    public const PREFIX = 'DEL';

    protected $fillable = [
        'company_id',
        'delivery_number',
        'order_id',
        'shipment_id',
        'driver_id',
        'customer_id',
        'status',
        'scheduled_date',
        'estimated_time',
        'actual_delivery_date',
        'delivery_time',
        'signature_required',
        'proof_of_delivery',
        'delivery_notes',
        'failure_reason',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_date' => 'date',
            'actual_delivery_date' => 'date',
            'delivery_time' => 'datetime',
            'signature_required' => 'boolean',
            'metadata' => 'array',
            'status' => DeliveryStatus::class,
        ];
    }

    public function getNumberPrefix(): string
    {
        return self::PREFIX;
    }

    public function getNumberColumn(): string
    {
        return 'delivery_number';
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Orders\Order::class);
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Shipping\Shipment::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Customers\Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(DeliveryItem::class);
    }
}
