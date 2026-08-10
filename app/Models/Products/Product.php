<?php

namespace App\Models\Products;

use App\Enums\Products\ProductStatus;
use App\Enums\Products\ProductType;
use App\Models\Traits\HasCompany;
use App\Models\Traits\HasNumber;
use App\Models\Traits\HasUuid;
use App\Models\Traits\SoftDeletes;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model
{
    use HasCompany, HasFactory, HasNumber, HasUuid, InteractsWithMedia, SoftDeletes;

    public const PREFIX = 'PRD';

    protected $fillable = [
        'company_id',
        'sku',
        'barcode',
        'name',
        'description',
        'short_description',
        'category_id',
        'brand_id',
        'unit_id',
        'product_type',
        'is_sellable',
        'is_purchasable',
        'is_stockable',
        'weight',
        'weight_unit',
        'dimensions',
        'cost_price',
        'selling_price',
        'minimum_price',
        'tax_rate',
        'reorder_level',
        'reorder_quantity',
        'minimum_order_quantity',
        'maximum_order_quantity',
        'shelf_life_days',
        'warranty_days',
        'status',
        'is_featured',
        'tags',
        'attributes',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'is_sellable' => 'boolean',
            'is_purchasable' => 'boolean',
            'is_stockable' => 'boolean',
            'weight' => 'decimal:3',
            'cost_price' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'minimum_price' => 'decimal:2',
            'tax_rate' => 'decimal:2',
            'is_featured' => 'boolean',
            'dimensions' => 'array',
            'tags' => 'array',
            'attributes' => 'array',
            'metadata' => 'array',
            'status' => ProductStatus::class,
            'product_type' => ProductType::class,
        ];
    }

    public function getNumberPrefix(): string
    {
        return self::PREFIX;
    }

    public function getNumberColumn(): string
    {
        return 'sku';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(ProductBrand::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(ProductUnit::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Media::class, 'model');
    }

    public function stockItems(): HasMany
    {
        return $this->hasMany(\App\Models\Inventory\StockItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(\App\Models\Orders\OrderItem::class);
    }
}
