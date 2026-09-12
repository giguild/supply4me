<?php

namespace App\Models\Inventory;

use App\Enums\Inventory\TransferItemCondition;
use App\Models\Traits\HasUuid;
use Database\Factories\StockTransferItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockTransferItem extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'transfer_id',
        'product_id',
        'variant_id',
        'bin_id',
        'quantity',
        'quantity_received',
        'condition',
        'unit_cost',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'quantity_received' => 'decimal:2',
            'condition' => TransferItemCondition::class,
            'unit_cost' => 'decimal:2',
        ];
    }

    public function transfer(): BelongsTo
    {
        return $this->belongsTo(StockTransfer::class, 'transfer_id');
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