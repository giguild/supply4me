<?php

namespace App\Models\Companies;

use App\Models\Traits\HasUuid;
use App\Models\Traits\SoftDeletes;
use Database\Factories\CompanyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'slug',
        'registration_number',
        'tax_number',
        'email',
        'phone',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'postal_code',
        'country',
        'currency_code',
        'status',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'settings' => 'array',
        ];
    }

    public function branches(): HasMany
    {
        return $this->hasMany(\App\Models\Branches\Branch::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(\App\Models\Core\User::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(\App\Models\Customers\Customer::class);
    }

    public function suppliers(): HasMany
    {
        return $this->hasMany(\App\Models\Suppliers\Supplier::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(\App\Models\Products\Product::class);
    }

    public function warehouses(): HasMany
    {
        return $this->hasMany(\App\Models\Inventory\Warehouse::class);
    }

    public function settings(): HasMany
    {
        return $this->hasMany(\App\Models\Settings\Setting::class);
    }
}
