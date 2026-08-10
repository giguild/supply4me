<?php

namespace App\Models\Receiving;

use App\Enums\Receiving\GRNStatus;
use App\Models\Traits\HasCompany;
use App\Models\Traits\HasNumber;
use App\Models\Traits\HasUuid;
use App\Models\Traits\SoftDeletes;
use Database\Factories\GoodsReceivedNoteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GoodsReceivedNote extends Model
{
    use HasCompany, HasFactory, HasNumber, HasUuid, SoftDeletes;

    public const PREFIX = 'GRN';

    protected $fillable = [
        'company_id',
        'grn_number',
        'purchase_order_id',
        'supplier_id',
        'warehouse_id',
        'status',
        'received_date',
        'notes',
        'received_by',
        'checked_by',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'received_date' => 'date',
            'metadata' => 'array',
            'status' => GRNStatus::class,
        ];
    }

    public function getNumberPrefix(): string
    {
        return self::PREFIX;
    }

    public function getNumberColumn(): string
    {
        return 'grn_number';
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(\App\Models\PurchaseOrders\PurchaseOrder::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Suppliers\Supplier::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Inventory\Warehouse::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(GoodsReceivedNoteItem::class);
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Core\User::class, 'received_by');
    }

    public function checkedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Core\User::class, 'checked_by');
    }
}
