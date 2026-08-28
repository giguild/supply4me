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
        'expected_quantity',
        'received_quantity',
        'accepted_quantity',
        'rejected_quantity',
        'rejection_reason',
        'batch_number',
        'serial_number',
        'expiry_date',
        'condition',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'expected_quantity' => 'decimal:2',
            'received_quantity' => 'decimal:2',
            'accepted_quantity' => 'decimal:2',
            'rejected_quantity' => 'decimal:2',
            'expiry_date' => 'date',
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
