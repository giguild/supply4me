<?php

namespace App\Models\Catalog;

use App\Models\Traits\HasCompany;
use App\Models\Traits\HasUuid;
use App\Models\Traits\SoftDeletes;
use Database\Factories\PromotionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Promotion extends Model
{
    use HasCompany, HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'company_id',
        'name',
        'description',
        'type',
        'value',
        'minimum_quantity',
        'minimum_amount',
        'maximum_discount',
        'valid_from',
        'valid_until',
        'usage_limit',
        'usage_count',
        'applicable_to',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'minimum_amount' => 'decimal:2',
            'maximum_discount' => 'decimal:2',
            'valid_from' => 'datetime',
            'valid_until' => 'datetime',
        ];
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Products\Product::class, 'promotion_products')
            ->withPivot('quantity', 'discount');
    }
}
