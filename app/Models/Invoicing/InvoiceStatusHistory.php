<?php

namespace App\Models\Invoicing;

use App\Enums\Invoicing\InvoiceStatus;
use App\Models\Traits\HasUuid;
use Database\Factories\InvoiceStatusHistoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceStatusHistory extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'invoice_status_history';

    protected $fillable = [
        'invoice_id',
        'status',
        'previous_status',
        'notes',
        'performed_by',
    ];

    protected function casts(): array
    {
        return [
            'status' => InvoiceStatus::class,
            'previous_status' => InvoiceStatus::class,
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Core\User::class, 'performed_by');
    }
}
