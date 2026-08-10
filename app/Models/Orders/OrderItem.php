<?php

namespace App\Models\Orders;

use App\Models\Traits\HasUuid;
use Database\Factories\OrderItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'order_id',
        'product_id',
        'variant_id',
        'unit_id',
        'sku',
        'name',
        'quantity',
        'unit_price',
        'discount_percentage',
        'tax_amount',
        'total_amount',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'discount_percentage' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Products\Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Products\ProductVariant::class, 'variant_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Products\ProductUnit::class, 'unit_id');
    }
}
