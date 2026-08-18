<?php

namespace App\Models\Core;

use App\Models\Companies\Company;
use App\Models\Traits\HasUuid;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, HasUuid, Notifiable, HasRoles;

    protected $fillable = [
        'company_id',
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'job_title',
        'department',
        'region',
        'state',
        'status',
        'last_login_at',
        'last_login_ip',
        'preferences',
        'metadata',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
            'preferences' => 'array',
            'metadata' => 'array',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Branches\Branch::class, 'user_branches')
            ->withPivot('is_default');
    }

    public function assignedOrders(): HasMany
    {
        return $this->hasMany(\App\Models\Orders\Order::class, 'assigned_to');
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(\App\Models\Delivery\Delivery::class);
    }

    public function assignedCustomers(): HasMany
    {
        return $this->hasMany(\App\Models\Customers\Customer::class, 'assigned_to');
    }

    /**
     * Get the guard name for this model.
     */
    public function guardName(): string
    {
        return 'web';
    }
}
