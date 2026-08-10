<?php

namespace App\Models\Products;

use App\Models\Traits\HasCompany;
use App\Models\Traits\HasUuid;
use Database\Factories\ProductUnitFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductUnit extends Model
{
    use HasCompany, HasFactory, HasUuid;

    protected $fillable = [
        'company_id',
        'name',
        'short_name',
        'base_unit_id',
        'conversion_factor',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'conversion_factor' => 'decimal:6',
        ];
    }

    public function baseUnit(): BelongsTo
    {
        return $this->belongsTo(self::class, 'base_unit_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
