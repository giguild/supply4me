<?php

namespace App\Models\Catalog;

use App\Models\Traits\HasUuid;
use Database\Factories\PriceListItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceListItem extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'price_list_id',
        'product_id',
        'price',
        'minimum_quantity',
        'discount_percentage',
        'valid_from',
        'valid_until',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount_percentage' => 'decimal:2',
            'valid_from' => 'date',
            'valid_until' => 'date',
        ];
    }

    public function priceList(): BelongsTo
    {
        return $this->belongsTo(PriceList::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Products\Product::class);
    }
}
