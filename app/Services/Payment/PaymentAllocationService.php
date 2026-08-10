<?php

namespace App\Services\Payment;

use App\Enums\Invoicing\InvoiceStatus;
use App\Enums\Payments\PaymentStatus;
use App\Models\Invoicing\Invoice;
use App\Models\Payments\Payment;
use App\Models\Payments\PaymentAllocation;
use App\ValueObjects\Money;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PaymentAllocationService
{
    /**
     * Automatically allocate a payment to the oldest outstanding invoices.
     */
    public function autoAllocate(Payment $payment): Collection
    {
        $allocations = collect();
        $remainingAmount = Money::from((float) $payment->amount, $payment->currency_code ?? 'USD');

        $invoices = Invoice::where('customer_id', $payment->customer_id)
            ->whereIn('status', [InvoiceStatus::Pending, InvoiceStatus::Sent, InvoiceStatus::Partial, InvoiceStatus::Overdue])
            ->where('balance_due', '>', 0)
            ->orderBy('due_date')
            ->get();

        foreach ($invoices as $invoice) {
            if ($remainingAmount->isZero() || $remainingAmount->isNegative()) {
                break;
            }

            $invoiceBalance = Money::from((float) $invoice->balance_due, $payment->currency_code ?? 'USD');

            if ($remainingAmount->getAmount() >= $invoiceBalance->getAmount()) {
                $allocateAmount = $invoiceBalance;
            } else {
                $allocateAmount = $remainingAmount;
            }

            if ($allocateAmount->isPositive()) {
                $allocation = $this->allocateToInvoice($payment, $invoice, $allocateAmount);
                $allocations->push($allocation);
                $remainingAmount = $remainingAmount->subtract($allocateAmount);
            }
        }

        return $allocations;
    }

    /**
     * Allocate a specific amount from a payment to an invoice.
     */
    public function allocateToInvoice(Payment $payment, Invoice $invoice, Money $amount): PaymentAllocation
    {
        if ($amount->isZero() || $amount->isNegative()) {
            throw new \InvalidArgumentException('Allocation amount must be positive.');
        }

        $invoiceBalance = Money::from((float) $invoice->balance_due, $payment->currency_code ?? 'USD');

        if ($amount->getAmount() > $invoiceBalance->getAmount()) {
            throw new \InvalidArgumentException(
                "Allocation amount ({$amount->format()}) exceeds invoice balance ({$invoiceBalance->format()})."
            );
        }

        return DB::transaction(function () use ($payment, $invoice, $amount) {
            $allocation = PaymentAllocation::create([
                'payment_id' => $payment->id,
                'invoice_id' => $invoice->id,
                'amount' => $amount->getAmount(),
            ]);

            $newAmountPaid = (float) $invoice->amount_paid + $amount->getAmount();
            $newBalanceDue = (float) $invoice->total_amount - $newAmountPaid;

            $invoice->update([
                'amount_paid' => $newAmountPaid,
                'balance_due' => max(0, $newBalanceDue),
                'status' => $newBalanceDue <= 0 ? InvoiceStatus::Paid : InvoiceStatus::Partial,
                'paid_at' => $newBalanceDue <= 0 ? now() : $invoice->paid_at,
            ]);

            $this->updatePaymentStatus($payment);

            return $allocation;
        });
    }

    /**
     * Get the unallocated amount of a payment.
     */
    public function getUnallocatedAmount(Payment $payment): Money
    {
        $totalAllocated = $payment->allocations->sum('amount');

        return Money::from(
            (float) $payment->amount - $totalAllocated,
            $payment->currency_code ?? 'USD'
        );
    }

    /**
     * Update payment status based on allocation state.
     */
    private function updatePaymentStatus(Payment $payment): void
    {
        $unallocated = $this->getUnallocatedAmount($payment);

        if ($unallocated->isZero()) {
            $payment->update(['status' => PaymentStatus::Completed]);
        } elseif ($unallocated->getAmount() < (float) $payment->amount) {
            $payment->update(['status' => PaymentStatus::Completed]);
        }
    }
}
