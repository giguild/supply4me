<?php

namespace App\Models\Customers;

use App\Models\Traits\HasUuid;
use Database\Factories\CustomerContactFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerContact extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'customer_id',
        'name',
        'position',
        'email',
        'phone',
        'mobile',
        'is_primary',
        'receives_invoices',
        'receives_orders',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'receives_invoices' => 'boolean',
            'receives_orders' => 'boolean',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
