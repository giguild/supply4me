<?php

namespace App\Models\Delivery;

use App\Models\Traits\HasUuid;
use Database\Factories\DeliveryItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryItem extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'delivery_id',
        'order_item_id',
        'product_id',
        'quantity',
        'quantity_delivered',
        'condition',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'quantity_delivered' => 'decimal:2',
        ];
    }

    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Orders\OrderItem::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Products\Product::class);
    }
}
