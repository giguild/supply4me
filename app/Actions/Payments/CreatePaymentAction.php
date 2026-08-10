<?php

namespace App\Actions\Payments;

use App\Enums\Payments\PaymentStatus;
use App\Events\Payments\PaymentCreated;
use App\Models\Core\User;
use App\Models\Payments\Payment;
use Illuminate\Support\Facades\DB;

class CreatePaymentAction
{
    public function execute(array $data, User $user): Payment
    {
        return DB::transaction(function () use ($data, $user) {
            $payment = Payment::create([
                'company_id' => $data['company_id'],
                'customer_id' => $data['customer_id'] ?? null,
                'supplier_id' => $data['supplier_id'] ?? null,
                'order_id' => $data['order_id'] ?? null,
                'invoice_id' => $data['invoice_id'] ?? null,
                'type' => $data['type'],
                'method' => $data['method'],
                'status' => PaymentStatus::Pending,
                'amount' => $data['amount'],
                'currency_code' => $data['currency_code'] ?? 'USD',
                'exchange_rate' => $data['exchange_rate'] ?? 1,
                'reference' => $data['reference'] ?? null,
                'notes' => $data['notes'] ?? null,
                'payment_date' => $data['payment_date'] ?? now()->toDateString(),
                'branch_id' => $data['branch_id'] ?? null,
                'received_by' => $user->id,
                'metadata' => $data['metadata'] ?? [],
            ]);

            event(new PaymentCreated($payment, $user));

            return $payment;
        });
    }
}
