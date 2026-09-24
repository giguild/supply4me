<?php

namespace App\Models\Payments;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PaymentReceipt extends Model
{
    use HasUuids;

    protected $fillable = [
        'payment_id',
        'receipt_path',
        'description',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
