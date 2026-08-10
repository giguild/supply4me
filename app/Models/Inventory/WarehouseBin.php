<?php

namespace App\Models\Inventory;

use App\Models\Traits\HasUuid;
use Database\Factories\WarehouseBinFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WarehouseBin extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'zone_id',
        'code',
        'aisle',
        'rack',
        'shelf',
        'bin',
        'capacity',
        'current_quantity',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'capacity' => 'decimal:2',
            'current_quantity' => 'decimal:2',
        ];
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(WarehouseZone::class);
    }

    public function stockItems(): HasMany
    {
        return $this->hasMany(StockItem::class, 'bin_id');
    }
}
