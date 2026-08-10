<?php

namespace App\Models\Catalog;

use App\Models\Traits\HasCompany;
use App\Models\Traits\HasUuid;
use App\Models\Traits\SoftDeletes;
use Database\Factories\PriceListFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PriceList extends Model
{
    use HasCompany, HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'company_id',
        'name',
        'description',
        'currency_code',
        'type',
        'priority',
        'valid_from',
        'valid_until',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'valid_from' => 'date',
            'valid_until' => 'date',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(PriceListItem::class);
    }

    public function customers(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Customers\Customer::class, 'customer_price_lists')
            ->withPivot('valid_from', 'valid_until');
    }
}
