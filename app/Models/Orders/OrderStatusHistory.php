<?php

namespace App\Models\Orders;

use App\Models\Traits\HasUuid;
use Database\Factories\OrderStatusHistoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderStatusHistory extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'order_status_history';

    protected $fillable = [
        'order_id',
        'status',
        'previous_status',
        'notes',
        'performed_by',
    ];

    protected function casts(): array
    {
        return [
            'status' => \App\Enums\Orders\OrderStatus::class,
            'previous_status' => \App\Enums\Orders\OrderStatus::class,
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Core\User::class, 'performed_by');
    }
}
