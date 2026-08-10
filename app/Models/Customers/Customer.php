<?php

namespace App\Models\Customers;

use App\Enums\Customers\CreditStatus;
use App\Enums\Customers\CustomerStatus;
use App\Enums\Customers\CustomerType;
use App\Models\Traits\HasCompany;
use App\Models\Traits\HasNumber;
use App\Models\Traits\HasUuid;
use App\Models\Traits\SoftDeletes;
use Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class Customer extends Authenticatable
{
    use HasCompany, HasFactory, HasNumber, HasUuid, SoftDeletes, Notifiable;

    public const PREFIX = 'CUST';

    protected $fillable = [
        'company_id',
        'customer_number',
        'name',
        'trade_name',
        'customer_type',
        'tax_number',
        'registration_number',
        'email',
        'password',
        'phone',
        'mobile',
        'fax',
        'website',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'postal_code',
        'country',
        'latitude',
        'longitude',
        'credit_limit',
        'payment_terms_days',
        'discount_percentage',
        'assigned_to',
        'price_list_id',
        'status',
        'credit_status',
        'notes',
        'metadata',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'credit_limit' => 'decimal:2',
            'discount_percentage' => 'decimal:2',
            'metadata' => 'array',
            'status' => CustomerStatus::class,
            'customer_type' => CustomerType::class,
            'credit_status' => CreditStatus::class,
            'password' => 'hashed',
        ];
    }

    public function getNumberPrefix(): string
    {
        return self::PREFIX;
    }

    public function getNumberColumn(): string
    {
        return 'customer_number';
    }

    public function getAuthPassword()
    {
        return $this->password;
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Core\User::class, 'assigned_to');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(CustomerContact::class);
    }

    public function shippingAddresses(): HasMany
    {
        return $this->hasMany(CustomerShippingAddress::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(\App\Models\Orders\Order::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(\App\Models\Invoicing\Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(\App\Models\Payments\Payment::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(CustomerNote::class);
    }
}
