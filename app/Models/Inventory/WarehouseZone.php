<?php

namespace App\Models\Inventory;

use App\Models\Traits\HasUuid;
use Database\Factories\WarehouseZoneFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WarehouseZone extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'warehouse_id',
        'name',
        'code',
        'type',
        'temperature_controlled',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'temperature_controlled' => 'boolean',
        ];
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function bins(): HasMany
    {
        return $this->hasMany(WarehouseBin::class);
    }
}
