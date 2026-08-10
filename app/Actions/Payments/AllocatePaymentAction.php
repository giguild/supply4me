<?php

namespace App\Actions\Payments;

use App\Enums\Payments\PaymentStatus;
use App\Models\Invoicing\Invoice;
use App\Models\Payments\Payment;
use App\Models\Payments\PaymentAllocation;
use Illuminate\Support\Facades\DB;

class AllocatePaymentAction
{
    public function execute(Payment $payment, array $allocations): PaymentAllocation
    {
        return DB::transaction(function () use ($payment, $allocations) {
            if ($payment->status !== PaymentStatus::Completed) {
                throw new \App\Exceptions\PaymentCannotBeAllocatedException(
                    'Payment must be completed before allocation.'
                );
            }

            $totalAllocated = collect($allocations)->sum('amount');
            $existingAllocated = $payment->allocations()->sum('amount');
            $availableAmount = $payment->amount - $existingAllocated;

            if ($totalAllocated > $availableAmount) {
                throw new \App\Exceptions\AllocationExceedsPaymentException(
                    'Total allocation exceeds available payment amount.'
                );
            }

            $allocation = null;
            foreach ($allocations as $allocationData) {
                $invoice = Invoice::findOrFail($allocationData['invoice_id']);

                $allocation = PaymentAllocation::create([
                    'payment_id' => $payment->id,
                    'invoice_id' => $invoice->id,
                    'amount' => $allocationData['amount'],
                    'notes' => $allocationData['notes'] ?? null,
                ]);

                $totalPaid = $invoice->allocations()->sum('amount') + $allocationData['amount'];
                $newBalance = $invoice->total_amount - $totalPaid;

                if ($newBalance <= 0) {
                    $invoice->update([
                        'status' => 'paid',
                        'amount_paid' => $invoice->total_amount,
                        'balance_due' => 0,
                        'paid_at' => now(),
                    ]);
                } else {
                    $invoice->update([
                        'status' => 'partial',
                        'amount_paid' => $totalPaid,
                        'balance_due' => $newBalance,
                    ]);
                }
            }

            return $allocation;
        });
    }
}
