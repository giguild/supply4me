<?php

namespace App\Models\Shipping;

use App\Models\Traits\HasUuid;
use Database\Factories\ShipmentItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentItem extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'shipment_id',
        'order_item_id',
        'product_id',
        'variant_id',
        'quantity',
        'weight',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'weight' => 'decimal:3',
        ];
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Orders\OrderItem::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Products\Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Products\ProductVariant::class, 'variant_id');
    }
}
