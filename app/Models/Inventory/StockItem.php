<?php

namespace App\Models\Inventory;

use App\Enums\Inventory\StockStatus;
use App\Models\Traits\HasCompany;
use App\Models\Traits\HasUuid;
use App\Models\Traits\SoftDeletes;
use Database\Factories\StockItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockItem extends Model
{
    use HasCompany, HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'company_id',
        'warehouse_id',
        'product_id',
        'variant_id',
        'bin_id',
        'quantity_on_hand',
        'quantity_reserved',
        'quantity_on_order',
        'reorder_level',
        'reorder_quantity',
        'last_counted_at',
        'last_received_at',
        'last_sold_at',
        'cost_price',
        'status',
        'version',
    ];

    protected function casts(): array
    {
        return [
            'quantity_on_hand' => 'decimal:2',
            'quantity_reserved' => 'decimal:2',
            'quantity_on_order' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'last_counted_at' => 'datetime',
            'last_received_at' => 'datetime',
            'last_sold_at' => 'datetime',
            'status' => StockStatus::class,
            'version' => 'integer',
        ];
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
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
        return $this->belongsTo(WarehouseBin::class, 'bin_id');
    }

    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function incrementVersion(): bool
    {
        return $this->increment('version');
    }
}
