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
use Illuminate\Support\Str;

class Product extends Model
{
    use HasCompany, HasFactory, HasNumber, HasUuid, SoftDeletes;

    public const PREFIX = 'PRD';

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Product $product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    protected $fillable = [
        'company_id',
        'sku',
        'barcode',
        'name',
        'slug',
        'product_images',
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
            'product_images' => 'array',
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
        return self::sku;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(ProductBrand::class, 'brand_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(ProductUnit::class, 'unit_id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
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
