<?php

namespace App\Models\Inventory;

use App\Enums\Inventory\TransferStatus;
use App\Models\Traits\HasCompany;
use App\Models\Traits\HasNumber;
use App\Models\Traits\HasUuid;
use App\Models\Traits\SoftDeletes;
use Database\Factories\StockTransferFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockTransfer extends Model
{
    use HasCompany, HasFactory, HasNumber, HasUuid, SoftDeletes;

    public const PREFIX = 'XFR';

    protected $fillable = [
        'company_id',
        'transfer_number',
        'from_warehouse_id',
        'to_warehouse_id',
        'status',
        'shipped_at',
        'received_at',
        'shipped_by',
        'received_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'shipped_at' => 'datetime',
            'received_at' => 'datetime',
            'status' => TransferStatus::class,
        ];
    }

    public function getNumberPrefix(): string
    {
        return self::PREFIX;
    }

    public function getNumberColumn(): string
    {
        return 'transfer_number';
    }

    public function fromWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }

    public function toWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockTransferItem::class, 'transfer_id');
    }

    public function shippedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Core\User::class, 'shipped_by');
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Core\User::class, 'received_by');
    }
}
