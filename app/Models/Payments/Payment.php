<?php

namespace App\Models\Payments;

use App\Enums\Payments\PaymentMethod;
use App\Enums\Payments\PaymentStatus;
use App\Enums\Payments\PaymentType;
use App\Models\Traits\HasCompany;
use App\Models\Traits\HasNumber;
use App\Models\Traits\HasUuid;
use App\Models\Traits\SoftDeletes;
use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    use HasCompany, HasFactory, HasNumber, HasUuid, SoftDeletes;

    public const PREFIX = 'PAY';

    protected $fillable = [
        'company_id',
        'payment_number',
        'customer_id',
        'supplier_id',
        'order_id',
        'invoice_id',
        'type',
        'method',
        'status',
        'amount',
        'currency_code',
        'exchange_rate',
        'reference',
        'notes',
        'payment_date',
        'cleared_date',
        'branch_id',
        'approved_by',
        'received_by',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'exchange_rate' => 'decimal:6',
            'payment_date' => 'date',
            'cleared_date' => 'date',
            'metadata' => 'array',
            'status' => PaymentStatus::class,
            'method' => PaymentMethod::class,
            'type' => PaymentType::class,
        ];
    }

    public function getNumberPrefix(): string
    {
        return self::PREFIX;
    }

    public function getNumberColumn(): string
    {
        return 'payment_number';
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Customers\Customer::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Suppliers\Supplier::class);
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(PaymentAllocation::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Core\User::class, 'approved_by');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Branches\Branch::class);
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Core\User::class, 'received_by');
    }
}
