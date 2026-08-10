<?php

namespace App\Services\Payment;

use App\Enums\Payments\PaymentStatus;
use App\Models\Payments\Payment;
use App\ValueObjects\Money;
use Illuminate\Support\Facades\DB;

class PaymentProcessingService
{
    public function __construct(
        private readonly PaymentAllocationService $allocationService,
    ) {}

    /**
     * Process a payment through the payment pipeline.
     */
    public function processPayment(Payment $payment): Payment
    {
        if (!$this->validatePayment($payment)) {
            throw new \InvalidArgumentException('Invalid payment data.');
        }

        DB::transaction(function () use ($payment) {
            $payment->update(['status' => PaymentStatus::Processing]);

            try {
                $payment->update([
                    'status' => PaymentStatus::Completed,
                    'cleared_date' => now()->toDateTimeString(),
                ]);

                if ($payment->customer_id && $payment->order_id) {
                    $this->allocationService->autoAllocate($payment);
                }
            } catch (\Exception $e) {
                $payment->update(['status' => PaymentStatus::Failed]);
                throw $e;
            }
        });

        return $payment->fresh();
    }

    /**
     * Validate payment data before processing.
     */
    public function validatePayment(Payment $payment): bool
    {
        if ($payment->amount <= 0) {
            throw new \InvalidArgumentException('Payment amount must be greater than zero.');
        }

        if (empty($payment->customer_id) && empty($payment->supplier_id)) {
            throw new \InvalidArgumentException('Payment must be linked to a customer or supplier.');
        }

        if (!$payment->method) {
            throw new \InvalidArgumentException('Payment method is required.');
        }

        if (!$payment->payment_date) {
            throw new \InvalidArgumentException('Payment date is required.');
        }

        return true;
    }

    /**
     * Generate a payment receipt.
     */
    public function generateReceipt(Payment $payment): array
    {
        $amount = Money::from((float) $payment->amount, $payment->currency_code ?? 'USD');

        return [
            'payment_number' => $payment->payment_number,
            'date' => $payment->payment_date->format('Y-m-d'),
            'amount' => $amount->format(),
            'method' => $payment->method->label(),
            'status' => $payment->status->label(),
            'customer' => $payment->customer?->name,
            'supplier' => $payment->supplier?->name,
            'reference' => $payment->reference,
            'notes' => $payment->notes,
            'allocations' => $payment->allocations->map(function ($allocation) {
                return [
                    'invoice_number' => $allocation->invoice?->invoice_number,
                    'amount' => Money::from((float) $allocation->amount, 'USD')->format(),
                ];
            }),
        ];
    }
}
