<?php

namespace App\Observers;

use App\Enums\Payments\PaymentStatus;
use App\Events\Payments\PaymentApproved;
use App\Events\Payments\PaymentCompleted;
use App\Events\Payments\PaymentCreated;
use App\Events\Payments\PaymentRejected;
use App\Events\Payments\PaymentRefunded;
use App\Models\Payments\Payment;
use Spatie\Activitylog\Facades\ActivityLog;

class PaymentObserver
{
    public function created(Payment $payment): void
    {
        ActivityLog::event('Payment created')
            ->on($payment)
            ->withProperties([
                'payment_id' => $payment->id,
                'payment_number' => $payment->payment_number,
                'amount' => $payment->amount,
                'customer_id' => $payment->customer_id,
                'status' => $payment->status->value,
                'method' => $payment->method->value,
                'company_id' => $payment->company_id,
            ])
            ->log();

        PaymentCreated::dispatch($payment);
    }

    public function updated(Payment $payment): void
    {
        $changes = $payment->getChanges();

        ActivityLog::event('Payment updated')
            ->on($payment)
            ->withProperties([
                'payment_id' => $payment->id,
                'payment_number' => $payment->payment_number,
                'attributes' => $changes,
                'old' => $payment->getOriginal(),
            ])
            ->log();

        if (isset($changes['status'])) {
            $oldStatus = PaymentStatus::tryFrom($payment->getOriginal('status'));
            $newStatus = PaymentStatus::tryFrom($changes['status']);

            $this->validateStatusTransition($oldStatus, $newStatus, $payment);

            match ($newStatus) {
                PaymentStatus::Completed => PaymentCompleted::dispatch($payment),
                PaymentStatus::Refunded => PaymentRefunded::dispatch($payment),
                default => null,
            };
        }

        if (isset($changes['approved_by']) && $changes['approved_by'] !== null) {
            PaymentApproved::dispatch($payment);
        }
    }

    public function deleted(Payment $payment): void
    {
        ActivityLog::event('Payment deleted')
            ->on($payment)
            ->withProperties([
                'payment_id' => $payment->id,
                'payment_number' => $payment->payment_number,
                'amount' => $payment->amount,
            ])
            ->log();
    }

    public function restored(Payment $payment): void
    {
        ActivityLog::event('Payment restored')
            ->on($payment)
            ->withProperties([
                'payment_id' => $payment->id,
            ])
            ->log();
    }

    protected function validateStatusTransition(
        ?PaymentStatus $oldStatus,
        ?PaymentStatus $newStatus,
        Payment $payment
    ): void {
        if ($oldStatus === null || $newStatus === null) {
            return;
        }

        $validTransitions = [
            PaymentStatus::Pending => [PaymentStatus::Processing, PaymentStatus::Cancelled],
            PaymentStatus::Processing => [PaymentStatus::Completed, PaymentStatus::Failed],
            PaymentStatus::Failed => [PaymentStatus::Pending, PaymentStatus::Cancelled],
            PaymentStatus::Completed => [PaymentStatus::Refunded],
        ];

        if (! in_array($newStatus, $validTransitions[$oldStatus] ?? [])) {
            ActivityLog::event('Invalid payment status transition attempted')
                ->on($payment)
                ->withProperties([
                    'old_status' => $oldStatus->value,
                    'new_status' => $newStatus->value,
                    'payment_id' => $payment->id,
                ])
                ->warning();
        }
    }
}
