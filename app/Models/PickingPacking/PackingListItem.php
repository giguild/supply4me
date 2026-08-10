<?php

namespace App\Models\PickingPacking;

use App\Models\Traits\HasUuid;
use Database\Factories\PackingListItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackingListItem extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'packing_list_id',
        'order_item_id',
        'product_id',
        'variant_id',
        'quantity',
        'weight',
        'dimensions',
        'package_type',
        'tracking_number',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'weight' => 'decimal:3',
            'dimensions' => 'array',
        ];
    }

    public function packingList(): BelongsTo
    {
        return $this->belongsTo(PackingList::class);
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
