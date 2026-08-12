<?php

namespace App\Models\Orders;

use App\Enums\Orders\FulfillmentStatus;
use App\Enums\Orders\OrderPriority;
use App\Enums\Orders\OrderStatus;
use App\Enums\Orders\OrderType;
use App\Enums\Orders\PaymentStatus;
use App\Models\Traits\HasCompany;
use App\Models\Traits\HasNumber;
use App\Models\Traits\HasUuid;
use App\Models\Traits\SoftDeletes;
use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\MediaLibrary\InteractsWithMedia;

class Order extends Model
{
    use HasCompany, HasFactory, HasNumber, HasUuid, InteractsWithMedia, SoftDeletes;

    public const PREFIX = 'ORD';

    protected $fillable = [
        'company_id',
        'order_number',
        'customer_id',
        'branch_id',
        'warehouse_id',
        'price_list_id',
        'order_type',
        'status',
        'payment_status',
        'fulfillment_status',
        'priority',
        'order_date',
        'requested_delivery_date',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'shipping_amount',
        'total_amount',
        'currency_code',
        'payment_terms_days',
        'due_date',
        'notes',
        'internal_notes',
        'shipping_address_id',
        'assigned_to',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'shipping_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'due_date' => 'date',
            'metadata' => 'array',
            'status' => OrderStatus::class,
            'order_type' => OrderType::class,
            'payment_status' => PaymentStatus::class,
            'fulfillment_status' => FulfillmentStatus::class,
            'priority' => OrderPriority::class,
        ];
    }

    public function getNumberPrefix(): string
    {
        return self::PREFIX;
    }

    public function getNumberColumn(): string
    {
        return 'order_number';
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Customers\Customer::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Branches\Branch::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Inventory\Warehouse::class);
    }

    public function priceList(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Catalog\PriceList::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(\App\Models\Invoicing\Invoice::class);
    }

    public function shippingAddress(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Customers\CustomerShippingAddress::class, 'shipping_address_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Core\User::class, 'assigned_to');
    }
}
