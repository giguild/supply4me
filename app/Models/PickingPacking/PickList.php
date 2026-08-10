<?php

namespace App\Models\PickingPacking;

use App\Enums\PickingPacking\PickListStatus;
use App\Models\Traits\HasCompany;
use App\Models\Traits\HasNumber;
use App\Models\Traits\HasUuid;
use App\Models\Traits\SoftDeletes;
use Database\Factories\PickListFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PickList extends Model
{
    use HasCompany, HasFactory, HasNumber, HasUuid, SoftDeletes;

    public const PREFIX = 'PKL';

    protected $fillable = [
        'company_id',
        'pick_list_number',
        'warehouse_id',
        'order_id',
        'status',
        'started_at',
        'completed_at',
        'picker_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'status' => PickListStatus::class,
        ];
    }

    public function getNumberPrefix(): string
    {
        return self::PREFIX;
    }

    public function getNumberColumn(): string
    {
        return 'pick_list_number';
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Inventory\Warehouse::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PickListItem::class);
    }

    public function picker(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Core\User::class, 'picker_id');
    }
}
