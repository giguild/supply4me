<?php

namespace App\Models\PickingPacking;

use App\Enums\PickingPacking\PackingStatus;
use App\Models\Traits\HasCompany;
use App\Models\Traits\HasNumber;
use App\Models\Traits\HasUuid;
use App\Models\Traits\SoftDeletes;
use Database\Factories\PackingListFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PackingList extends Model
{
    use HasCompany, HasFactory, HasNumber, HasUuid, SoftDeletes;

    public const PREFIX = 'PACK';

    protected $fillable = [
        'company_id',
        'packing_list_number',
        'order_id',
        'warehouse_id',
        'status',
        'started_at',
        'completed_at',
        'packer_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'status' => PackingStatus::class,
        ];
    }

    public function getNumberPrefix(): string
    {
        return self::PREFIX;
    }

    public function getNumberColumn(): string
    {
        return 'packing_list_number';
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Orders\Order::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Inventory\Warehouse::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PackingListItem::class);
    }

    public function packer(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Core\User::class, 'packer_id');
    }
}
