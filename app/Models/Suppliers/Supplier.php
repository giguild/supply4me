<?php

namespace App\Models\Suppliers;

use App\Models\Traits\HasCompany;
use App\Models\Traits\HasNumber;
use App\Models\Traits\HasUuid;
use App\Models\Traits\SoftDeletes;
use Database\Factories\SupplierFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasCompany, HasFactory, HasNumber, HasUuid, SoftDeletes;

    public const PREFIX = 'SUP';

    protected $fillable = [
        'company_id',
        'supplier_number',
        'name',
        'contact_person',
        'tax_number',
        'email',
        'phone',
        'mobile',
        'website',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'postal_code',
        'country',
        'payment_terms_days',
        'lead_time_days',
        'minimum_order_amount',
        'rating',
        'bank_name',
        'bank_account_name',
        'bank_account_number',
        'bank_routing_number',
        'bank_swift_code',
        'status',
        'notes',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'minimum_order_amount' => 'decimal:2',
            'rating' => 'decimal:2',
            'metadata' => 'array',
        ];
    }

    public function getNumberPrefix(): string
    {
        return self::PREFIX;
    }

    public function getNumberColumn(): string
    {
        return 'supplier_number';
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Products\Product::class, 'supplier_products')
            ->withPivot('cost_price', 'lead_time_days', 'minimum_quantity');
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(\App\Models\PurchaseOrders\PurchaseOrder::class);
    }
}
