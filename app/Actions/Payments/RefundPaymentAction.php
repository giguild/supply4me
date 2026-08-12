<?php

namespace App\Actions\Payments;

use App\Enums\Payments\PaymentStatus;
use App\Events\Payments\PaymentRefunded;
use App\Models\Core\User;
use App\Models\Payments\Payment;
use Illuminate\Support\Facades\DB;

class RefundPaymentAction
{
    public function execute(Payment $payment, User $user, ?string $reason = null): Payment
    {
        if ($payment->status !== PaymentStatus::Completed) {
            throw new \App\Exceptions\PaymentCannotBeRefundedException(
                'Only completed payments can be refunded.'
            );
        }

        return DB::transaction(function () use ($payment, $user, $reason) {
            $payment->update([
                'status' => PaymentStatus::Cancelled,
                'notes' => $reason ? ($payment->notes ? $payment->notes . "\nRefund reason: " . $reason : "Refund reason: " . $reason) : $payment->notes,
            ]);

            event(new PaymentRefunded($payment, $user));

            return $payment->fresh();
        });
    }
}
