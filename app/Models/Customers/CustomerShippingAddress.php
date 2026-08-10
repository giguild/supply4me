<?php

namespace App\Models\Customers;

use App\Models\Traits\HasUuid;
use App\Models\Traits\SoftDeletes;
use Database\Factories\CustomerShippingAddressFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerShippingAddress extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'label',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'postal_code',
        'country',
        'latitude',
        'longitude',
        'delivery_instructions',
        'is_default',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'is_default' => 'boolean',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
