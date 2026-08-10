<?php

namespace App\Models\PickingPacking;

use App\Enums\PickingPacking\PickItemStatus;
use App\Models\Traits\HasUuid;
use Database\Factories\PickListItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PickListItem extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'pick_list_id',
        'order_id',
        'order_item_id',
        'product_id',
        'variant_id',
        'bin_id',
        'quantity_to_pick',
        'quantity_picked',
        'status',
        'picked_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity_to_pick' => 'decimal:2',
            'quantity_picked' => 'decimal:2',
            'picked_at' => 'datetime',
            'status' => PickItemStatus::class,
        ];
    }

    public function pickList(): BelongsTo
    {
        return $this->belongsTo(PickList::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Orders\Order::class);
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

    public function bin(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Inventory\WarehouseBin::class, 'bin_id');
    }
}
