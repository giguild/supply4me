<?php

namespace App\Models\Receiving;

use App\Models\Traits\HasUuid;
use Database\Factories\GoodsReceivedNoteItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoodsReceivedNoteItem extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'grn_id',
        'purchase_order_item_id',
        'product_id',
        'variant_id',
        'bin_id',
        'quantity_ordered',
        'quantity_received',
        'quantity_accepted',
        'quantity_rejected',
        'condition',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity_ordered' => 'decimal:2',
            'quantity_received' => 'decimal:2',
            'quantity_accepted' => 'decimal:2',
            'quantity_rejected' => 'decimal:2',
        ];
    }

    public function grn(): BelongsTo
    {
        return $this->belongsTo(GoodsReceivedNote::class);
    }

    public function purchaseOrderItem(): BelongsTo
    {
        return $this->belongsTo(\App\Models\PurchaseOrders\PurchaseOrderItem::class);
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
