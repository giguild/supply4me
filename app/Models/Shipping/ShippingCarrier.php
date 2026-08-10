<?php

namespace App\Models\Shipping;

use App\Enums\Shipping\CarrierStatus;
use App\Models\Traits\HasCompany;
use App\Models\Traits\HasUuid;
use Database\Factories\ShippingCarrierFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShippingCarrier extends Model
{
    use HasCompany, HasFactory, HasUuid;

    protected $fillable = [
        'company_id',
        'name',
        'code',
        'api_key',
        'api_secret',
        'tracking_url',
        'supports_tracking',
        'supports_label_generation',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'supports_tracking' => 'boolean',
            'supports_label_generation' => 'boolean',
            'status' => CarrierStatus::class,
        ];
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }
}
