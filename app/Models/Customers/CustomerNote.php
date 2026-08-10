<?php

namespace App\Models\Customers;

use App\Models\Traits\HasUuid;
use Database\Factories\CustomerNoteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerNote extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'customer_id',
        'user_id',
        'note',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Core\User::class);
    }
}
