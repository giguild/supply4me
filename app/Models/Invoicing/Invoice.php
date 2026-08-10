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
        'type',
        'status',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'amount_paid',
        'balance_due',
        'currency_code',
        'due_date',
        'paid_at',
        'notes',
        'terms',
        'created_by',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'balance_due' => 'decimal:2',
            'due_date' => 'date',
            'paid_at' => 'datetime',
            'metadata' => 'array',
            'status' => InvoiceStatus::class,
            'type' => InvoiceType::class,
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

    public function statusHistory(): HasMany
    {
        return $this->hasMany(InvoiceStatusHistory::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Core\User::class, 'created_by');
    }
}
