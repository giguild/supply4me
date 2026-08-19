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
        'payment_type',
        'payment_method',
        'reference_number',
        'check_number',
        'bank_name',
        'bank_account_number',
        'amount',
        'currency_code',
        'exchange_rate',
        'payment_date',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'notes',
        'branch_id',
        'received_by',
        'version',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'exchange_rate' => 'decimal:6',
            'payment_date' => 'date:Y-m-d',
            'approved_at' => 'datetime',
            'metadata' => 'array',
            'status' => PaymentStatus::class,
            'payment_method' => PaymentMethod::class,
            'payment_type' => PaymentType::class,
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
