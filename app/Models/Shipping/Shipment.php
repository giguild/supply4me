<?php

namespace App\Models\Shipping;

use App\Enums\Shipping\ShipmentStatus;
use App\Models\Traits\HasCompany;
use App\Models\Traits\HasNumber;
use App\Models\Traits\HasUuid;
use App\Models\Traits\SoftDeletes;
use Database\Factories\ShipmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shipment extends Model
{
    use HasCompany, HasFactory, HasNumber, HasUuid, SoftDeletes;

    public const PREFIX = 'SHP';

    protected $fillable = [
        'company_id',
        'shipment_number',
        'order_id',
        'warehouse_id',
        'carrier_id',
        'status',
        'tracking_number',
        'shipping_method',
        'estimated_delivery_date',
        'actual_delivery_date',
        'shipping_cost',
        'weight',
        'dimensions',
        'shipped_at',
        'delivered_at',
        'notes',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'estimated_delivery_date' => 'date',
            'actual_delivery_date' => 'date',
            'shipping_cost' => 'decimal:2',
            'weight' => 'decimal:3',
            'dimensions' => 'array',
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
            'metadata' => 'array',
            'status' => ShipmentStatus::class,
        ];
    }

    public function getNumberPrefix(): string
    {
        return self::PREFIX;
    }

    public function getNumberColumn(): string
    {
        return 'shipment_number';
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Orders\Order::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Inventory\Warehouse::class);
    }

    public function carrier(): BelongsTo
    {
        return $this->belongsTo(ShippingCarrier::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ShipmentItem::class);
    }
}
