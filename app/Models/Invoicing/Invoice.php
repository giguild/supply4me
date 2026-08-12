<?php

namespace App\Models\Invoicing;

use App\Enums\Invoicing\InvoiceStatus;
use App\Enums\Invoicing\InvoiceType;
use App\Models\Traits\HasCompany;
use App\Models\Traits\HasNumber;
use App\Models\Traits\HasUuid;
use App\Models\Traits\SoftDeletes;
use Database\Factories\InvoiceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\InteractsWithMedia;

class Invoice extends Model
{
    use HasCompany, HasFactory, HasNumber, HasUuid, InteractsWithMedia, SoftDeletes;

    public const PREFIX = 'INV';

    protected $fillable = [
        'company_id',
        'invoice_number',
        'customer_id',
        'order_id',
        'invoice_type',
        'status',
        'invoice_date',
        'due_date',
        'currency_code',
        'exchange_rate',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'shipping_amount',
        'total_amount',
        'paid_amount',
        'due_amount',
        'payment_terms_days',
        'notes',
        'terms_conditions',
        'sent_at',
        'viewed_at',
        'cancelled_at',
        'cancellation_reason',
        'created_by',
        'version',
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
            'paid_amount' => 'decimal:2',
            'due_amount' => 'decimal:2',
            'due_date' => 'date',
            'invoice_date' => 'date',
            'sent_at' => 'datetime',
            'viewed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'metadata' => 'array',
            'status' => InvoiceStatus::class,
            'invoice_type' => InvoiceType::class,
        ];
    }

    public function getNumberPrefix(): string
    {
        return self::PREFIX;
    }

    public function getNumberColumn(): string
    {
        return 'invoice_number';
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Customers\Customer::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Orders\Order::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments()
    {
        return $this->hasManyThrough(
            \App\Models\Payments\Payment::class,
            \App\Models\Payments\PaymentAllocation::class,
            'invoice_id',
            'id',
            'id',
            'payment_id'
        );
    }

    public function allocations()
    {
        return $this->hasMany(\App\Models\Payments\PaymentAllocation::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(InvoiceStatusHistory::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Core\User::class, 'created_by');
    }
}
