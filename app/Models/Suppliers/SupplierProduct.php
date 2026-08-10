<?php

namespace App\Models\Suppliers;

use App\Models\Traits\HasUuid;
use Database\Factories\SupplierProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierProduct extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'supplier_id',
        'product_id',
        'cost_price',
        'lead_time_days',
        'minimum_quantity',
        'sku',
        'is_preferred',
    ];

    protected function casts(): array
    {
        return [
            'cost_price' => 'decimal:2',
            'is_preferred' => 'boolean',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Products\Product::class);
    }
}
