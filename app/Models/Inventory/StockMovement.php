<?php

namespace App\Models\Inventory;

use App\Enums\Inventory\MovementType;
use App\Models\Traits\HasCompany;
use App\Models\Traits\HasUuid;
use Database\Factories\StockMovementFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use HasCompany, HasFactory, HasUuid;

    protected $fillable = [
        'company_id',
        'stock_item_id',
        'movement_type',
        'quantity',
        'quantity_before',
        'quantity_after',
        'reference_type',
        'reference_id',
        'from_warehouse_id',
        'to_warehouse_id',
        'from_bin_id',
        'to_bin_id',
        'unit_cost',
        'total_cost',
        'reason',
        'performed_by',
        'approved_by',
        'approved_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'quantity_before' => 'decimal:2',
            'quantity_after' => 'decimal:2',
            'unit_cost' => 'decimal:2',
            'total_cost' => 'decimal:2',
            'approved_at' => 'datetime',
            'movement_type' => MovementType::class,
        ];
    }

    public function stockItem(): BelongsTo
    {
        return $this->belongsTo(StockItem::class);
    }

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Core\User::class, 'performed_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Core\User::class, 'approved_by');
    }
}
